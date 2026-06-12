<?php
// ========================================
// app/Models/Event.php
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'client_id', 'type', 'event_date',
        'location', 'budget', 'status', 'description',
    ];

    protected $casts = ['event_date' => 'date'];

    public function client()    { return $this->belongsTo(Client::class); }
    public function rabItems()  { return $this->hasMany(RabItem::class); }
    public function payments()  { return $this->hasMany(Payment::class); }
    public function tasks()     { return $this->hasMany(Task::class); }
}

// ========================================
// app/Models/Client.php
// ========================================
// namespace App\Models;
// use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function events()   { return $this->hasMany(Event::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}

// ========================================
// app/Models/ClientRequest.php
// ========================================
// namespace App\Models;
// use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    protected $fillable = [
        'client_name', 'event_name', 'event_type',
        'event_date', 'location', 'budget', 'status', 'notes',
    ];
}

// ========================================
// app/Models/RabItem.php
// ========================================
// namespace App\Models;
// use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    protected $fillable = [
        'event_id', 'item_name', 'category',
        'unit', 'qty', 'unit_price', 'total_price', 'notes',
    ];

    public function event() { return $this->belongsTo(Event::class); }
}

// ========================================
// app/Models/Payment.php
// ========================================
// namespace App\Models;
// use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'event_id', 'client_id', 'invoice_number',
        'amount', 'status', 'proof_file', 'notes',
    ];

    public function event()  { return $this->belongsTo(Event::class); }
    public function client() { return $this->belongsTo(Client::class); }
}

// ========================================
// app/Models/Task.php
// ========================================
// namespace App\Models;
// use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'event_id', 'title', 'description',
        'assigned_to', 'due_date', 'status',
    ];

    public function event() { return $this->belongsTo(Event::class); }
}
