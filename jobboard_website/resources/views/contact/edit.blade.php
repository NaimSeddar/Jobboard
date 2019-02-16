@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div id="card-header" class="card-header">Modifier mon profile</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('storeContactChange') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="civilite" class="col-md-4 col-form-label text-md-right">Civilité</label>

                                <div class="col-md-6">
                                    <select class="form-control" id="civilite" name="civilite">
                                        <option value="Monsieur" {{$contact->civilite == "Monsieur" ? 'selected' : ''}}>Monsieur</option>
                                        <option value="Madame" {{$contact->civilite == "Madame" ? 'selected' : ''}}>Madame</option>
                                        <option value="Autre" {{$contact->civilite == "Autre" ? 'selected' : ''}}>Autre</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nom" class="col-md-4 col-form-label text-md-right">{{ __('Nom') }}</label>

                                <div class="col-md-6">
                                    <input id="nom" type="text" class="form-control{{ $errors->has('nom') ? ' is-invalid' : '' }}" name="nom" value="{{ $contact->nom }}" required>

                                    @if ($errors->has('nom'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('nom') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="prenom" class="col-md-4 col-form-label text-md-right">{{ __('Prenom') }}</label>

                                <div class="col-md-6">
                                    <input id="prenom" type="text" class="form-control{{ $errors->has('prenom') ? ' is-invalid' : '' }}" name="prenom" value="{{ $contact->prenom }}" required>

                                    @if ($errors->has('prenom'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('prenom') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Adresse E-mail') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $contact->mail }}" required>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div id="divEntreprise">
                                <div class="form-group row">
                                    <label for="telephone" class="col-md-4 col-form-label text-md-right">Numéro de téléphone</label>

                                    <div class="col-md-6">
                                        <input id="telephone" type="text" class="form-control" name="telephone" value="{{$contact->telephone}}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Modifier mon profile') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

