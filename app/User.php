<?php

namespace App;

use Google_Service_Plus_Person;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\NewsFeed;
use DateTime as DT;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'url', 'password', 'token', 'google_id', 'avatar', 'language', 'theme', 'api_token', 'last_login',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'token' => 'array',
    ];

    /**
     * @param $user
     */
    protected static function debugAuth($action, $user)
    {
        \Log::debug($action .
            PHP_EOL .
            print_r($user->toArray(), 1)
        );
    }

    /**
     * Fetch the user by Google+ ID
     *
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeGPlusMember($query, $id)
    {
        return $query->where('google_id', (string) $id);
    }

    /**
     * Create new member based on Google+ [/me] info
     *
     * @param Google_Service_Plus_Person $gPlusUser
     * @param null $token
     * @return static
     */
    static public function fromGPlusUser(Google_Service_Plus_Person $gPlusUser, $token = null)
    {
        if (!$user = static::GPlusMember($gPlusUser->getId())->first()) {
            $user = static::create([
                'google_id' => (string) $gPlusUser->getId(),
                'name' => $gPlusUser->getDisplayName(),
                'email' => $gPlusUser->getEmails()[0]->getValue(),
                'url' => $gPlusUser->getUrl() ?: null,
                'avatar' => $gPlusUser->getImage()->getUrl(),
                'token' => json_decode($token, true),
            ]);

            $feeds = NewsFeed::where('user_id', null)->get();
            foreach ($feeds as $feed) {
                NewsFeed::create([
                    'user_id' => $user->id,
                    'language' => null,
                    'name' => $feed->name,
                    'url' => $feed->url,
                    'categories' => $feed->categories
                ]);
            }

            self::debugAuth("New user created", $user);
        } else {
            self::debugAuth("User logged in", $user);
        }

        if (!is_null($token)) {
            $userToken = (array) $user->token;
            $token = (array) json_decode($token, true);

            foreach ($userToken as $key => $value) {
                if (array_has($token, $key) && $token[$key] !== $value) {
                    $userToken[$key] = $token[$key];
                }
            }

            $user->fill([
                'token' => $userToken,
            ])->save();
        }

        return $user;
    }

    /**
     * Refresh `refresh_token` required for Server-Server calls
     *
     * @param $refreshToken
     * @return $this
     */
    public function refreshToken($refreshToken)
    {
        $this->token = array_merge((array) $this->token, [
            'refresh_token' => $refreshToken,
        ]);
        $this->save();

        return $this;
    }

    /**
     * Retrieve user's preffered language.
     *
     * @return mixed
     */
    public function lang()
    {
        return $this->language;
    }

    public function theme()
    {
        return $this->theme;
    }

    public function savePreference($key, $value)
    {
        switch ($key) {
            case 'language':
                $this->language = $value;
                break;

            case 'theme':
                $this->theme = $this->validatedTheme($value);
                break;

            default:
                throw new \Exception("Unknown preference: {$key}");
        }

        $this->save();

        return $this;
    }

    protected function validatedTheme($theme)
    {
        $themes = config('app.themes');

        if (!in_array($theme, $themes)) {
            $theme = array_first($themes);
        }

        return $theme;
    }
    
    public function setLastLogin() {
        $this->last_login = new DT();
        $this->save();

        return $this;
    }
}
