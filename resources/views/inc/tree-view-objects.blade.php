<div>
    Object List
    <ul id="objectList"></ul>
    <ul id="lightList"></ul>
</div>

@push('script')
    <script>
        // This function will populate the ul elements with the object names and light names.
        function updateObjectNamesFromScene() {
            let objectNames = scene.meshes.map(function (mesh) {
                return mesh.name;
            });

            let lights = scene.lights.map(function (light) {
                return light.name;
            });

            // Get references to the ul elements
            let objectList = document.getElementById("objectList");
            let lightList = document.getElementById("lightList");

            // Clear existing items in the lists
            objectList.innerHTML = "";
            lightList.innerHTML = "";

            // Add object names to the objectList
            objectNames.forEach(function (name) {
                let li = document.createElement("li");
                li.textContent = name;
                li.addEventListener("click", function () {
                    selectObject(name);
                });

                objectList.appendChild(li);
            });

            // Add light names to the lightList
            lights.forEach(function (name) {
                let li = document.createElement("li");
                li.textContent = name;
                li.addEventListener("click", function () {
                    selectLight(name);
                });
                lightList.appendChild(li);
            });
        }
        // Your selectObject function
        function selectObject(objectName) {
            // Find the corresponding mesh by name
            let selectedMesh = scene.getMeshByName(objectName);

            if (selectedMesh) {
                // Add your gizmo and shadow generation logic here
                gizmoManager.attachToMesh(selectedMesh);
                selectedMesh.receiveShadows = true;
                shadowGenerator.addShadowCaster(selectedMesh, true);
                gizmoManager.positionGizmoEnabled = true;
                gizmoManager.rotationGizmoEnabled = false;
                gizmoManager.scaleGizmoEnabled = false;

                // Add the "ObjectSelect" class to the selected object's <li> element
                let objectList = document.getElementById("objectList");
                let objectItems = objectList.getElementsByTagName("li");

                for (let i = 0; i < objectItems.length; i++) {
                    let item = objectItems[i];
                    if (item.textContent === objectName) {
                        item.classList.add("ObjectSelect");
                    } else {
                        item.classList.remove("ObjectSelect");
                    }
                }
            }
        }
        // Your selectLight function
        function selectLight(lightName) {
            // Find the corresponding light by name
            let selectedLight = scene.getLightByName(lightName);

            if (selectedLight) {
                gizmoManager.attachToNode(selectedLight);
                // Add your logic for selecting a light here
                // For example, you can change its properties or perform other actions
                console.log("Selected light: " + selectedLight.name);
            }
        }
        // Call the function to initially populate the lists
        updateObjectNamesFromScene();
    </script>
@endpush
