<?php namespace Bonsum;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Carbon\Carbon;
use App;


class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['firstname', 'lastname', 'gender', 'email', 'preferred_locale', 'referer_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'old_password', 'remember_token', 'confirmation_code',
	'reset_token', 'merchant_transactions', 'bonets_credits', 'bonets_redeems', 'bonets_donations'];

	/**
	 * Fields automatically mutated into Carbon instances
	 * @var array
	 */
	protected $dates = ['confirmation_code_creation', 'reset_token_creation', 'disabled_at'];

	protected $casts = [

		'disabled' => 'boolean',
		'confirmed' => 'boolean',
		'admin' => 'boolean',
		'id' => 'integer',
		'bonets' => 'integer',
		'confirmation_reminder_sent' => 'boolean',
		'referer_id' => 'integer'
	];

	public function toArray() {

		$array = parent::toArray();
		$array['created_at'] = ($this->created_at instanceof Carbon ? $this->created_at->toDateString() : $this->created_at);
		$array['disabled_at'] = ($this->disabled_at instanceof Carbon ? $this->disabled_at->toDateString() : $this->disabled_at);
		$array['reset_token_creation'] = ($this->reset_token_creation instanceof Carbon ? $this->reset_token_creation->toDateString() : $this->reset_token_creation);

		return $array;
	}

	public function merchant_transactions() {

		return $this->hasMany('Bonsum\MerchantTransaction');
	}

	public function getFullNameAttribute() {
		return $this->firstname . ' ' . $this->lastname;
	}

	public function bonets_credits() {
		return $this->hasMany('Bonsum\BonetsCredit');
	}

	public function bonets_donations() {
		return $this->hasMany('Bonsum\BonetsDonation');
	}

	public function bonets_redeems() {
		return $this->hasMany('Bonsum\BonetsRedeem');
	}

	public function getAdminAttribute() {
		return (!empty($this->attributes['admin']) && App::environment() !== 'production' && !$this->normal_user_mode);
	}

	public function referer() {
		return $this->belongsTo('Bonsum\User', 'referer_id');
	}

}
