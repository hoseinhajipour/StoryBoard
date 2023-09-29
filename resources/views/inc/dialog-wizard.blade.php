<div class="text-center">
    <!-- Button trigger modal -->
    <button type="button" onclick="initWizard()" class="btn btn-primary my-3">
        Open Dialog Wizard
    </button>

    <!-- Modal -->
    <div class="modal fade" id="DialogWizardModal" tabindex="-1" aria-labelledby="DialogWizardModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dialog Wizard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div id="dialog_row_template" class="row d-none dialog_row">
                        <div class="col-1">
                            <button class="btn btn-danger form-control"><span class="fa fa-trash"></span></button>
                        </div>
                        <div class="col-2">
                            <label>Character</label>
                            <select id="CharacterList" class="character_select form-control">
                                <option value="character01">character 01</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label>dialog Title</label>
                            <input type="text" class="dialog_title form-control">
                            <label>Audio file</label><br/>
                            <input type="file" class="audio_select" onchange="applyAudioFile(event)">
                            <audio class="audio_player" controls></audio>
                        </div>

                        <div class="col-2">
                            <label>animation</label>
                            <select class="animation_select form-control">
                                @foreach($animations as $animation)
                                    <option
                                        value="{{ url('storage/'.str_replace("\\", "/", json_decode($animation->url)[0]->download_link))  }}">{{$animation->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <label>Camera</label>
                            <select id="camera_list" class="form-control">
                                <option value="cam01">camera 01</option>
                            </select>
                        </div>
                    </div>

                    <div id="dialogs">

                    </div>

                    <button onclick="AppendNewRow()" type="button" class="btn btn-primary"><span
                            class="fa fa-plus"></span></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="CreateAnimation()" class="btn btn-primary">Create</button>
                </div>
            </div>
        </div>
    </div>


    @push('script')
        <script>
            function GetAllCharacters() {
                // Assuming scene.meshes is an array of objects with uniqueId and name properties
                let objectNames = scene.meshes.map(function (mesh) {
                    return {id: mesh.uniqueId, name: mesh.name};
                });

                // Get a reference to the CharacterList dropdown element
                let characterListDropdown = document.getElementById("CharacterList");

                // Clear the existing options in the dropdown
                characterListDropdown.innerHTML = "";

                // Create and append new option elements based on the objectNames array
                objectNames.forEach(function (obj) {
                    let option = document.createElement("option");
                    option.value = obj.id;
                    option.textContent = obj.name;
                    characterListDropdown.appendChild(option);
                });
            }

            function GetAllCamera() {
                // Assuming scene.meshes is an array of objects with uniqueId and name properties
                let cameras = scene.cameras.map(function (mesh) {
                    return {id: mesh.uniqueId, name: mesh.name};
                });

                // Get a reference to the CharacterList dropdown element
                let ListDropdown = document.getElementById("camera_list");

                // Clear the existing options in the dropdown
                ListDropdown.innerHTML = "";

                // Create and append new option elements based on the objectNames array
                cameras.forEach(function (obj) {
                    let option = document.createElement("option");
                    option.value = obj.id;
                    option.textContent = obj.name;
                    ListDropdown.appendChild(option);
                });
            }

            function initWizard() {
                $('#DialogWizardModal').modal('show');
                GetAllCharacters();
                GetAllCamera();
            }

            function AppendNewRow() {
                // Clone the dialog_row_template
                const template = document.getElementById('dialog_row_template');
                const newRow = template.cloneNode(true);

                // Remove the 'd-none' class to make it visible
                newRow.classList.remove('d-none');

                // Append the cloned row to the 'dialogs' div
                const dialogsDiv = document.getElementById('dialogs');
                dialogsDiv.appendChild(newRow);
            }

            function findNodeByUniqueId(uniqueId) {
                for (var i = 0; i < scene.meshes.length; i++) {
                    if (scene.meshes[i].uniqueId === uniqueId) {
                        return scene.meshes[i];
                    }
                }

                // If not found among meshes, you can extend this function to search in other types of nodes (lights, cameras, etc.) as needed.

                return null; // Node with uniqueId not found in the scene
            }

            function applyAudioFile(event) {
                var input = event.target;
                var audioPlayer = input.closest('.dialog_row').querySelector('.audio_player');

                var file = input.files[0];
                var reader = new FileReader();

                reader.onloadend = function () {
                    audioPlayer.src = reader.result;
                };

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    audioPlayer.src = ""; // Reset the audio src if no file is selected
                }
            }

            function searchForBlendShape(node) {
                // Check if the current node has a blend shape

                if (node.morphTargetManager) {
                    // If the node has a blend shape, return it
                    return node;
                } else {
                    // If the current node does not have a blend shape, check its children
                    if (node.getChildren) {
                        // Iterate through the child nodes
                        for (let i = 0; i < node.getChildren().length; i++) {
                            const childNode = node.getChildren()[i];
                            // Recursively search for blend shape in child nodes
                            const result = searchForBlendShape(childNode);
                            // If a node with blend shape is found, return it
                            if (result) {
                                return result;
                            }
                        }
                    }
                    // If no child node has blend shape, return null
                    return null;
                }
            }


            //------------------------------------------------//
            function generateAnimations() {
                const dialogRows = document.querySelectorAll('#dialogs .dialog_row');
                const animations = [];
                const characters = [];

                const promises = Array.from(dialogRows).map(async (row) => {
                    const animationSelect = row.querySelector('.animation_select');
                    const selectedAnimation = animationSelect.value;

                    animations.push(selectedAnimation);

                    const characterSelect = row.querySelector('.character_select');
                    let selectedCharacter = scene.getMeshByUniqueID(parseInt(characterSelect.value));

                    characters.push(selectedCharacter);

                    const dialogTitle = row.querySelector('.dialog_title');

                    // Call applyAnimationToCharacter and wait for its completion
                    await applyAnimationToCharacter(selectedAnimation, dialogTitle.value, selectedCharacter);
                });

                // Use Promise.all to wait for all promises to complete
                Promise.all(promises)
                    .then(() => {
                        console.log(animations);
                        console.log(characters);
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            }

            function CreateAnimation() {


                const dialogRows = document.querySelectorAll('#dialogs .dialog_row');
                var start_frame = 0;
                console.log("start frame : " + start_frame);
                dialogRows.forEach(function (row) {
                    var audioInput = $(row).find('.audio_select')[0];
                    var dialog_title = $(row).find('.dialog_title').val();
                    var selectedAnimation = $(row).find('.animation_select').val();
                    var characterSelect = parseInt($(row).find('.character_select').val());
                    var selectedCharacter = scene.getMeshByUniqueID(characterSelect);
                    var audio_player = $(row).find('.audio_player');
                    var audio_duration = audio_player[0].duration;
                    var _HeadMesh = searchForBlendShape(selectedCharacter);
                    var nextframe = start_frame;

                    if (audioInput.files.length > 0 && _HeadMesh) {
                        var formData = new FormData();
                        formData.append('audioFile', audioInput.files[0]);

                        $.ajax({
                            url: 'http://127.0.0.1:8000/api/generatePhonemes',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                // Handle success response here
                                console.log('File uploaded successfully:', response);
                                var phonemes = JSON.parse(response.phonemes);
                                lipSync_(phonemes, audio_duration, _HeadMesh, dialog_title, nextframe, response.audio_url);
                                applyAnimationToCharacter(selectedAnimation, dialog_title, selectedCharacter, nextframe);
                            },
                            error: function (error) {
                                // Handle error here
                                console.error('Error uploading file:', error);
                            }
                        });
                        start_frame += secondsToFrames(audio_duration, frameRate);
                    }


                });

                //1- upload all audio
                //2- generate all lip sync animation
                //3- append character animations
                // generateAnimations();
                //4- create camera animation switch

            }

        </script>
    @endpush


</div>
