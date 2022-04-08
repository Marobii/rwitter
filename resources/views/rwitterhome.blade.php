@extends('layouts.app')

@section('content')
    {{-- Form --}}
    <div class="container-lg spacing form-group">
        <form action="/rwit" method="POST">
            @csrf
            <textarea class="form-control" rows="5" cols="50" name="message"  id="message" placeholder="What´s Happening?" tabindex="4"></textarea>
            <div class="d-flex flex-row">
                <button type="submit" class="btn btn-primary btn-lg "><span>Rwit</span></button>
                <div class="p-3 align-self-center"><p>{{ session('info') }}</p></div>
            </div>
        </form>
    </div>

    {{-- Rwittes Content --}}
    <div class="container-lg spacing">
        <div class="d-flex p-2">Feed:</div>
        @foreach($qas as $index => $faq)
        <div class="d-flex flex-row sbtop">
            <img src="/img/circle.jpg" alt="User Logo" class="rounded-circle">
            <div class="p-2 align-self-center"><span>{{ Auth::user()->name }}</span></div>
        </div>

        <div class="container-lg d-flex bd-highlight myrtlist smtop">
            <div class="p-2 align-self-center w-100 bd-highlight myrtext text-justify">{{ $faq->message }}</div>
            <div class="p-2 flex-shrink-1 bd-highlight">
                <form action="/rwitterhome/{{ $faq->id }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger fa-regular fa-trash-can" onclick="return confirm('Pretende eliminar o rwit?')"></button>
                </form>
            </div>
            {{-- <form action="/funfa/{{ $faq->id }}" method="POST">
                @csrf
                <div class="d-flex flex-row">
                    <button type="submit" class="btn btn-primary btn-lg "><span>Rwit</span></button>
                </div>
            </form> --}}

            <form action="/funfa/{{$faq->id }}" method="POST">
                @csrf
                <button class="btn btn-danger fa-regular fa-trash-can"></button>
            </form>

        </div>
        @endforeach
    </div>

@endsection
