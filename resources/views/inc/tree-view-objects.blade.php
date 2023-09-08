<div class="my-2">
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                        aria-expanded="true" aria-controls="collapseOne">
                    Object List
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body bg-dark text-white">
                    <ul id="objectList"></ul>
                    <ul id="lightList"></ul>
                    <ul id="AnimationList"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        // This function will populate the ul elements with the object names and light names.
        function updateObjectNamesFromScene() {
            let objectNames = scene.meshes.map(function (mesh) {
                return {id: mesh.uniqueId, name: mesh.name};
            });


            let lights = scene.lights.map(function (light) {
                return {id: light.uniqueId, name: light.name};
            });


            // Get references to the ul elements
            let objectList = document.getElementById("objectList");
            let lightList = document.getElementById("lightList");

            // Clear existing items in the lists
            objectList.innerHTML = "";
            lightList.innerHTML = "";

            // Add object names to the objectList
            objectNames.forEach(function (obj) {
                let li = document.createElement("li");
                li.textContent = obj.name;
                li.setAttribute("data-id", obj.id); // Set the data-id attribute with the object's ID
                li.addEventListener("click", function () {
                    selectObject(obj.id); // Pass the object's ID when selecting
                });

                objectList.appendChild(li);
            });


            // Add light names to the lightList
            lights.forEach(function (light) {
                let li = document.createElement("li");
                li.textContent = light.name;
                li.addEventListener("click", function () {
                    selectLight(light.name);
                });
                lightList.appendChild(li);
            });

            let animations = scene.animationGroups.map(function (anim) {
                return {id: anim.uniqueId, name: anim.name};
            });
            let AnimationList = document.getElementById("AnimationList");
            AnimationList.innerHTML = "";


            animations.forEach(function (animation, index) {
                let anim = document.createElement("li");
                anim.textContent = animation.name;
                anim.setAttribute("data-id", index); // Set the data-id attribute with the object's ID
                anim.addEventListener("click", function () {
                    selectAnim(index); // Pass the current index when selecting
                });
                AnimationList.appendChild(anim);
            });

            console.log(scene.skeletons);
        }

        function selectAnim(RowID) {
            let NewAnim = scene.animationGroups[RowID];
            NewAnim.play();
            console.log(NewAnim);
        }

        // Modify the selectObject function to accept an object ID
        function selectObject(objectId) {
            // Find the corresponding mesh by ID
            let selectedMesh_ = scene.getMeshByUniqueID(objectId);

            if (selectedMesh_) {
                HeadMesh = selectedMesh_;
                selectedMesh = selectedMesh_;
                // Add your gizmo and shadow generation logic here
                gizmoManager.attachToMesh(selectedMesh_);
                selectedMesh_.receiveShadows = true;
                shadowGenerator.addShadowCaster(selectedMesh_, true);
                gizmoManager.positionGizmoEnabled = true;
                gizmoManager.rotationGizmoEnabled = false;
                gizmoManager.scaleGizmoEnabled = false;

                // Add the "ObjectSelect" class to the selected object's <li> element
                let objectList = document.getElementById("objectList");
                let objectItems = objectList.getElementsByTagName("li");

                for (let i = 0; i < objectItems.length; i++) {
                    let item = objectItems[i];
                    if (item.getAttribute("data-id") === objectId.toString()) {
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