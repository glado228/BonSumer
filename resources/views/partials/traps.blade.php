@foreach (\Bonsum\Http\Middleware\Traps::$traps as $trap)
	{!! Form::text($trap, null, ['class' => 'important-field']) !!}
@endforeach