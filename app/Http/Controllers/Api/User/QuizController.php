<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use App\Models\Users\Quiz;

class QuizController extends BaseController
{
    public function index(\App\Filters\UserQuizFilters $filters, Request $request)
    {
        $authUser = $request->user();

        $rows = $authUser->quizzes()->filter($filters)->distinct()->get();
        
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

    public function setAnswer(Quiz $quiz, Request $request)
    {
        $authUser = $request->user();

        $this->validate($request, [
            'question_id' => 'required',
            'answer_id' => 'required',
        ], [
            'question_id.required' => 'Необходимо указать вопрос',
            'answer_id.required' => 'Необходимо указать ответ',
        ]);

        $question = $quiz->questions()->where('id', $request->input('question_id'))->first()->load(['answers']);
        $answer = $question->answers->where('id', $request->input('answer_id'))->first();
        $correctAnswer = $question->answers->where('is_correct', true)->first();

        if( $quiz->answers()->whereIn('aid', $question->answers->pluck('id'))->count() )
        {
            $quiz->answers()->detach($question->answers->pluck('id'));
        }

        if( !$quiz->answers()->where('aid', $answer->id)->count() )
        {
            $quiz
            ->answers()
            ->attach(
                $answer->id,
                [
                    'point' => $request->filled('point') ? $request->input('point') : ( $correctAnswer->id == $answer->id ? 1 : 0),
                    'time' => $request->filled('time') ? $request->input('time') : null,
                    'text' => $request->filled('text') ? $request->input('text') : null,
                    'uid' => $authUser->id,
                    'qid' => $question->id,
                ]
            );
        }

        if( $quiz->answers->count() >= $quiz->questions->count() )
        {
            $quiz->update([
                'status_id' => 200,
            ]);
        }

        $membersHasQuizzes = $quiz->user->team->members()
        ->whereHas('quizzes', function($query) use ($quiz) {
            return $query
                        ->whereHas('quizze', function($q) use ($quiz) {
                            return $q->where('hash', $quiz->quizze->hash);
                        })
                        ->where('status_id', 200);
        })
        ->count();

        if( $quiz->user->team->members->count() == $membersHasQuizzes )
        {
            if( !$quiz->user->team->tasks()->where('slug', $quiz->quizze->hash)->exists() )
            {
                $quiz->user->team->tasks()->attach(\App\Models\Task::where('slug', $quiz->quizze->hash)->first()->id);
            }
            event(new \App\Events\UpdateTeamInfoEvent($quiz->user->team));
        }

        event(new \App\Events\UpdateUserInfoEvent($authUser));

        return $this->sendResponse($data = [], $message = 'Информация изменена', $code = 200, $isRaw = false);
    }

    public function unSetAnswer(Quiz $quiz, Request $request)
    {
        $authUser = $request->user();

        $this->validate($request, [
            'question_id' => 'required',
            'answer_id' => 'required',
        ], [
            'question_id.required' => 'Необходимо указать вопрос',
            'answer_id.required' => 'Необходимо указать ответ',
        ]);
        
        $question = $quiz->questions()->where('id', $request->input('question_id'))->first()->load(['answers']);
        $answer = $question->answers->where('id', $request->input('answer_id'))->first();
        
        if( $quiz->answers()->where('aid', $answer->id)->count() )
        {
            $quiz->answers()->detach($answer->id);
        }
        
        $membersHasQuizzes = $quiz->user->team->members()
        ->whereHas('quizzes', function($query) use ($quiz) {
            return $query
                        ->whereHas('quizze', function($q) use ($quiz) {
                            return $q->where('hash', $quiz->quizze->hash);
                        })
                        ->where('status_id', 200);
        })
        ->count();

        if( $quiz->answers->count() < $quiz->questions->count() )
        {
            $quiz->update([
                'status_id' => 100,
            ]);
        }

        if( $quiz->user->team->members->count() == $membersHasQuizzes )
        {
            if( $quiz->user->team->tasks()->where('slug', $quiz->quizze->hash)->exists() )
            {
                $quiz->user->team->tasks()->detach(\App\Models\Task::where('slug', $quiz->quizze->hash)->first()->id);
            }
            event(new \App\Events\UpdateTeamInfoEvent($quiz->user->team));
        }
        event(new \App\Events\UpdateUserInfoEvent($authUser));
        
        return $this->sendResponse($data = [], $message = 'Информация изменена', $code = 200, $isRaw = false);
    }
}
