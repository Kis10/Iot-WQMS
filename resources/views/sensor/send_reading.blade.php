@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Send Sensor Reading</h1>
    <form method="POST" action="{{ route('sensor.readings') }}">
        @csrf
        <div>
            <label>Device ID:</label>
            <input type="text" name="device_id" value="ESP32-WQ-01">
        </div>
        <div>
            <label>pH Level:</label>
            <input type="text" name="ph_level" value="7.0">
        </div>
        <div>
            <label>Turbidity:</label>
            <input type="text" name="turbidity" value="5">
        </div>
        <div>
            <label>TDS:</label>
            <input type="text" name="tds" value="500">
        </div>
        <div>
            <label>Water Temp:</label>
            <input type="text" name="temperature" value="25.0">
        </div>
        <button type="submit">Send Reading</button>
    </form>
</div>
@endsection
