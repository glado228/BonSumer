{{-- Angualar templates --}}

<script type="text/ng-template" id="modal_dialog.html">
        <div class="modal-content" data-ng-click="$event.stopPropagation()">
          <div class="modal-header">
              <h4 class="modal-title">@{{title}}</h4>
          </div>
          <div class="modal-body">
              <p>
                @{{message}}
              </p>
          </div>
          <div class="modal-footer">
              <button class="btn btn-primary" ng-click="$close('good')">@{{ok_label}}</button>
              <button data-ng-hide="hide_cancel" class="btn btn-warning" ng-click="$dismiss('bad')">@{{cancel_label}}</button>
          </div>
        </div>
  </script>
  <script type="text/ng-template" id="progress_dialog.html">
        <div class="modal-content" data-ng-click="$event.stopPropagation()">
          <div class="modal-header">
              <h4 class="modal-title">@{{title}}</h4>
          </div>
          <div class="modal-body">
            <div class="progress">
              <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
              </div>
            </div>
          </div>
          <div class="modal-footer">
              <button class="btn btn-warning" ng-click="$dismiss('bad')">@{{cancel_label}}</button>
          </div>
        </div>
  </script>
