<?php 
namespace Mediabit\Templates\Sections;


class Modal {

    public function render()

    {

        $modal_html = <<<HTML
        
        <div class="modal fade" id="symptompsModal" tabindex="-1" aria-labelledby="symptompsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="symptompsModalLabel">Symptomps</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input id="symptoms-input" class="form-control" placeholder="Type symptomp"/> 
                    <div id="symptoms-list"></div>
                    <button class="mt-2 btn btn-primary" id="symptoms-add-handler">Save Symptoms</button>
                </div>
                </div>
            </div>
        </div>
        
        HTML;

        return $modal_html;

    }

}
?>