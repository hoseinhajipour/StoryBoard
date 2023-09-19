<div class="container">

    <div class="card shadow my-5">
        <div class="card-body">
            <label>Project Title</label>
            <input wire:model="title" class="form-control">
            <button wire:click="CreateNewProject()" class="btn btn-primary">Create +</button>
        </div>
    </div>

    <div class="row">
        @foreach($projects as $project)
            <div class="col-4">
                <div class="card shadow text-center my-2">
                    <div class="card-body">
                        <button wire:click="DeleteProject({{$project->id}})" class="btn btn-danger position-absolute "><span
                                class="fa fa-trash"></span></button>

                        <a href="{{route("editor",["id"=>$project->id])}}" class="no_link text-dark">

                            @if($project->icon)
                                <img src="{{Voyager::image($project->icon)}}" width="100%">
                            @else
                                <img src="{{asset('img/empty_project.png')}}" width="100%">
                            @endif
                            <b class="my-3">{{$project->title}}</b>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
