<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Organization;
use App\Models\User;
use App\Models\Media;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportTeams implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_name;

    public function __construct($file_name)
    {
        $this->file_name = $file_name;
    }

    public function handle()
    {
        
        $array = collect((new \App\Imports\TeamsImport)->toArray(Storage::disk('local')->path('public/uploads/tmp/' . $this->file_name))[0])->skip(1)->values();
        $formatedArray = collect();
        
        if( $array->count() )
        {
            foreach ($array as $key => $usr)
            {
                if( !empty(trim($usr[7])) )
                {
                    $formatedArray->push([
                        'request_id' => trim($usr[0]),
                        'organization' => trim($usr[2]),
                        'team' => trim($usr[3]),
                        'full_name' => trim($usr[4]),
                        'job_position' => trim($usr[5]),
                        'job_experience' => !empty($usr[6]) ? trim((int) $usr[6]) . ' ' .self::number(trim((int) $usr[6]), array('год', 'года', 'лет')) : null,
                        'email' => trim($usr[7]),
                        'lead' => strtolower(trim($usr[8])) == strtolower('Да') ? true : false,
                    ]);
                }
            }
        }
                
        $lists = $formatedArray->groupBy('request_id')->values();
        $UsersFields = \App\Models\Users\Field::all();
        
        if( $lists->count() )
        {
            foreach ($lists as $teamkey => $members)
            {
                $team_name = $members[0]['team'];
                $organization_name = $members[0]['organization'];

                $organization = \App\Models\Organization::firstOrCreate([
                    'name' => $organization_name
                ]);

                $team = \App\Models\Team::create([
                    'name' => $team_name,
                    'organization_id' => optional($organization)->id,
                    'description' => null,
                    'values' => null,
                ]);

                $team->steps()->attach(1);

                if( $members->count() )
                {
                    foreach ($members as $memberkey => $member)
                    {
                        $member['full_name'] = \Illuminate\Support\Str::of($member['full_name'])->explode(' ');

                        $user = \App\Models\User::create([
                            'email' => $member['email'],
                            'password' => bcrypt(Str::random(8)),
                            'organization_id' => optional($organization)->id,
                        ]);

                        $token = $user->createToken('default')->plainTextToken;

                        $user->update([
                            'api_token' => $token
                        ]);
                        
                        if( !empty($member['full_name'][0]) )
                        {
                            $field = $UsersFields->where('slug', 'last_name')->first();

                            $user->fields()->attach($field->id, [
                                'value' => $member['full_name'][0],
                                'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $UsersFields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                                'is_show' => 0
                            ]);
                        }

                        if( !empty($member['full_name'][1]) )
                        {
                            $field = $UsersFields->where('slug', 'first_name')->first();

                            $user->fields()->attach($field->id, [
                                'value' => $member['full_name'][1],
                                'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $UsersFields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                                'is_show' => 0
                            ]);
                        }

                        if( !empty($member['full_name'][2]) )
                        {
                            $field = $UsersFields->where('slug', 'middle_name')->first();

                            $user->fields()->attach($field->id, [
                                'value' => $member['full_name'][2],
                                'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $UsersFields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                                'is_show' => 0
                            ]);
                        }

                        if( !empty($member['job_position']) )
                        {
                            $field = $UsersFields->where('slug', 'job_position')->first();

                            $user->fields()->attach($field->id, [
                                'value' => $member['job_position'],
                                'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $UsersFields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                                'is_show' => 0
                            ]);
                        }

                        if( !empty($member['job_experience']) )
                        {
                            $field = $UsersFields->where('slug', 'job_experience')->first();

                            $user->fields()->attach($field->id, [
                                'value' => $member['job_experience'],
                                'points' => !empty($field->options) && !empty($field->options['points']) ? ( $field->options['points']['group'] == true ? ( $user->fields()->whereIn('field_id', $UsersFields->where('group_id', $field->group_id)->pluck('id'))->count() > 0 ? 0 : $field->options['points']['value'] ) : $field->options['points']['value'] ) : 0,
                                'is_show' => 0
                            ]);
                        }

                        $user->assignRole(\Spatie\Permission\Models\Role::where('name', 'member')->get());

                        $team->members()->attach($user->id);

                        if( !empty($member['lead']) && $member['lead'] )
                        {
                            $team->leads()->attach($user->id);
                        }

                        // \App\Jobs\EmailUsersNotificationJob::dispatch(
                        //     $user,
                        //     (new \App\Notifications\MessageNotification(
                        //         $sender = \App\Models\User::withTrashed()->where('email', env('MAIL_ADMINISTRATOR_EMAIL'))->first(),
                        //         $subject = 'Подтвердите свои данные',
                        //         $message = '<h1>Здравствуйте!</h1><p><br></p><p>Вы получили это письмо, так как зарегистрировались в конкурсе «Проектная активация» в составе команды <strong>«' . $team->name . '»</strong>.</p><p><br></p><p>Для подтверждения регистрации нажмите на кнопку ниже.</p><p><br></p><p>Напоминаем, что первый этап конкурса начнется <strong>8 сентября</strong>. Все дальнейшие инструкции будут направлены вам на электронную почту.</p>',
                        //         $button = [
                        //             'text' => 'Подтвердить регистрацию',
                        //             'url' => url(route('verification.show.index', $user)),
                        //         ],
                        //         $notification_id = null,
                        //     ))
                        // );
                    }
                }
            }
        }
    }

    public function number($n, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
    }
}
