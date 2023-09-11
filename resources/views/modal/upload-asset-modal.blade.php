<div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Upload Assets <span class="fa fa-plus"></span>
    </button>

    <!-- Modal -->
    <div wire:ignore class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Upload Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark">
                    <label>Type</label>
                    <select wire:model.defer="type" class="form-control my-2">
                        <option value="Scene">Scene</option>
                        <option value="Character">Character</option>
                        <option value="Animation">Animation</option>
                        <option value="Props">Props</option>
                    </select>
                    <label>Title</label>
                    <input wire:model.defer="title" type="text" class="form-control">

                    <form wire:submit.prevent="uploadFile">
                        <label>thumbnail Image</label>
                        <br/>
                        <input type="file" wire:model.defer="thumbnail">
                        <br/>
                        <label>Glb File</label>
                        <br/>
                        <input type="file" wire:model.defer="glbfile">
                        <br/>
                        <button class="btn btn-primary form-control my-3" type="submit">Upload</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
