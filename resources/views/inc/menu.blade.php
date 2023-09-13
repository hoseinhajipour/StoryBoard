<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a href="{{ route('index') }}" class="navbar-brand">
            <i class="fa fa-code text-primary"></i> {{ config('app.name') }}
        </a>

        <button type="button" data-bs-toggle="collapse" data-bs-target="#nav" class="navbar-toggler">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="nav" class="collapse navbar-collapse">
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle">
                        File
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" onclick="SaveScene()" class="dropdown-item">Save</a>
                        <a href="#" onclick="ExportScene()" class="dropdown-item">Export</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle">
                        Edit
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" class="dropdown-item"></a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle">
                        Create
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" class="dropdown-item">Add Camera</a>
                        <a href="#" onclick="AddCube()" class="dropdown-item">Add Cube</a>
                        <a href="#" class="dropdown-item">Add Plane</a>
                        <a href="#" class="dropdown-item">Add Light</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle">
                        Render
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" class="dropdown-item"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var objectUrl;
        var filename = "test";

        function SaveScene() {
            BABYLON.Tools.CreateScreenshot(engine, Maincamera, 400, function (imagedata) {
                if (objectUrl) {
                    window.URL.revokeObjectURL(objectUrl);
                }
                var serializedScene = BABYLON.SceneSerializer.Serialize(scene);
                var serializedSceneJson = JSON.stringify(serializedScene);
                Livewire.emit('SaveProject', serializedSceneJson,imagedata);

            });




        }
    </script>
</nav>
