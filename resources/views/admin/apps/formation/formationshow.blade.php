{{-- @extends('layouts.admin.master')

@section('content')
    <div class="container">
        <h1>{{ $formation->title }}</h1>
        <p><strong>Description:</strong> {{ $formation->description }}</p>
        <p><strong>Durée:</strong> {{ $formation->duration }}</p>
        <p><strong>Type:</strong> {{ $formation->type }}</p>
        <p><strong>price:</strong> {{ $formation->price }}</p>

        <p><strong>Catégorie:</strong> {{ $formation->categorie->title }}</p>
        <a href="{{ route('formations') }}" class="btn btn-secondary">Retour à la lise </a>
    </div>
@endsection --}}

@extends('layouts.admin.master')
@section('title')
    Liste des Formations
@endsection

@push('css')


@endpush

@section('content')
<!-- Section pour afficher les quiz publiés -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Quiz associés</h4>
        </div>
        <div class="card-body">
            @if($formation->quizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>

                                <th>Durée (minutes)</th>
                                <th>Score minimum</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formation->quizzes as $quiz)
                                <tr>
                                    <td>{{ $quiz->title }}</td>

                                    <td>{{ $quiz->duration }}</td>
                                    <td>{{ $quiz->isFinalQuiz() ? $quiz->passing_score . '/20' :  'le score depend des nombre des reponses correctes ' }}</td>
                                    <td>

                                        @if(auth()->user()->hasRole('etudiant'))
                                        <form method="POST" action="{{ route('quizzes.start', $quiz->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-play"></i> Démarrer
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Aucun quiz publié n'est disponible pour cette formation.
                </div>
            @endif
        </div>
    </div>
@endsection
