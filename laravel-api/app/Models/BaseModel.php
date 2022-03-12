<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class BaseModel extends Model{
    use SoftDeletes;

    public $incrementing = false;

    public static function boot() {
        parent::boot();

        static::creating(function($model) {
            // Only generate the uuid if the field actually is called uuid.
            // For some system models a normal id is used (e.g. language)
            //this was changed. I now use id as the field name
            if($model->getKeyName() == 'id'){
                if($model->id != ""){

                }else
                    $model->{$model->getKeyName()} = (string)$model->generateNewId();
            }
        });
    }

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Exception
     */
    public function generateNewId()
    {
        return Uuid::uuid4();
    }

    /**
     * @param array $options
     * @return bool|void
     * @throws \Exception
     */
    public function save (array $options = array())
    {
        try{
            parent::save($options);
        }catch(\Exception $e){
            // check if the exception is caused by double id
            if(preg_match('/Integrity constraint violation: 1062 Duplicate entry \S+ for key \'PRIMARY\'/', $e->getMessage(), $matches)){
                $this->{$this->getKeyName()} = (string)$this->generateNewId();
                $this->save();
            }
        }
    }

    /**
     * Encrypt passwords
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    protected $hidden = [
        'password'
    ];
}
