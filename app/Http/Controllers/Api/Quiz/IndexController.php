<?php

namespace App\Http\Controllers\Api\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Quiz\Quiz;
use App\Models\Users\Quiz AS UserQuiz;

class IndexController extends BaseController
{
    public function index(\App\Filters\QuizFilters $filters, Request $request)
    {
        $rows = Quiz::query()->filter($filters)->distinct()->get();
        
        if( $request->filled('with') && is_array($request->input('with')) )
        {
            if( $rows instanceof Quiz )
            {
                $rows = $rows->load($request->input('with'));
            }
            else
            {
                $rows = $rows->each->load($request->input('with'));
            }
        }

        if( $request->filled('appends') && is_array($request->input('appends')) )
        {
            if( $rows instanceof Quiz )
            {
                $rows = $rows->setAppends($request->input('appends'));
            }
            else
            {
                $rows = $rows->each->setAppends($request->input('appends'));
            }
        }

        if( $request->filled('pagination') )
        {
            $request->merge([
                'pagination' => json_decode($request->input('pagination'), true)
            ]);

            $rows = $rows->paginate(
                $perPage = ( $request->filled('pagination.perPage') ? $request->input('pagination.perPage') : 15 ),
                $pageName = ( $request->filled('pagination.pageName') ? $request->input('pagination.pageName') : 'page' ),
                $page = ( $request->filled('pagination.page') ? $request->input('pagination.page') : ( $request->filled('page') ? $request->input('page') : null ) )
            );
            return $this->sendResponse($data = $rows, $message = null, $code = 200, $isRaw = false);
        }

        return $this->sendResponse($data = $rows, $message = null, $code = 200, $isRaw = false);
    }

    public function initialisation(Request $request)
    {
        $authUser = $request->user();

        $this->validate($request, [
            'hash' => 'required',
            'team_id' => 'required',
            'step_id' => 'required',
            'task_id' => 'required',
        ], [
            'hash.required' => 'Необходимо указать идентификатор',
            'team_id.required' => 'Необходимо указать команду',
            'step_id.required' => 'Необходимо указать этап',
            'task_id.required' => 'Необходимо указать задачу',
        ]);

        $quiz = Quiz::with(['questions', 'questions.theme', 'themes'])->where('hash', $request->input('hash'))->first();

        if( empty($quiz) )
        {
            return $this->sendError('Данные не найдены', $errorMessages = [], $code = 422);
        }

        $themes = collect($quiz->themes)
        ->map(
            function($theme)
            {
                return [
                    'id' => $theme->id,
                    'name' => $theme->name,
                    'quizze_id' => $theme->quizze_id,
                    'questions-count-per-user' => !empty($theme->options) && !empty($theme->options['questions-count-per-user']) ? $theme->options['questions-count-per-user'] : 10,
                    'slug' => $theme->slug,
                ];
            }
        )
        ->values();

        $userQuiz = UserQuiz::with(['questions', 'answers', 'quizze'])
            ->where(
                function($query) use ($quiz, $authUser)
                {
                    return $query
                            ->where('quizze_id', $quiz->id)
                            ->where('user_id', $authUser->id);
                }
            )
            ->get();
        
        if( $userQuiz->count() )
        {
            if( $userQuiz->where('status_id', 200)->count() )
            {
                foreach ($userQuiz->where('status_id', 200) as $row)
                {
                    $row->delete();
                }
            }

            if( $userQuiz->where('status_id', 100)->count() )
            {
                return $this->sendError('Существующий квиз не пройден', $errorMessages = [], $code = 208);
            }
        }

        $quizStore = $authUser->quizzes()->create([
            'name' => $quiz->name,
            'description' => $quiz->description,
            'quizze_id' => $quiz->id,
            'status_id' => 100,
            'team_id' => $request->input('team_id'),
            'step_id' => $request->input('step_id'),
            'task_id' => $request->input('task_id'),
        ]);

        if( !empty($quizStore) )
        {

            if( !empty($themes) )
            {
                foreach ($themes as $theme)
                {
                    $questions = $quiz->questions->where('theme_id', $theme['id'])->random($theme['questions-count-per-user']);
                    
                    if( !empty($questions) )
                    {
                        foreach ($questions as $question)
                        {
                            $quizStore->questions()->attach($question->id, [
                                'uid' => $quizStore->user_id,
                            ]);
                        }
                    }
                }
            }
        }

        return $this->sendResponse($data = [], $message = null, $code = 200, $isRaw = false);
    }
}
