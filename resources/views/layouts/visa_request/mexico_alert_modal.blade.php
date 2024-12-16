<div class="modal" tabindex="-1" role="dialog" id="mexicoAlertModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cancelMexicoAlert">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>We have few restriction for applying Mexico Work Permit. We suggest you to kindly reach out to Mexico HR Partner (Manivannan Ravi) or Global Mobility (Raghavan Sethuraman) for further information. If you have already discussed with them, please go ahead and submit the request.</p>
        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="secondary-button" data-dismiss="modal" id="cancelMexicoAlert" style="margin-right: 10px">Cancel</button> --}}
          <button type="button" class="primary-button" data-dismiss="modal">Okay</button>
        </div>
      </div>
    </div>
</div>

<style>
    #mexicoAlertModal .modal-dialog {
        margin-top: 0; margin-bottom: 0;
        top: 50%;
        transform: translateY(-50%);
    }
    #mexicoAlertModal .modal-header {
      display: flex;
    }
    #mexicoAlertModal .modal-header h5 {
      flex: 1;
    }

    #mexicoAlertModal .modal-body {
        font-family: mont-medium;
        font-size: 12px;
    }

    #mexicoAlertModal .modal-footer {
      border-top: unset;
    }
</style>

<script>
  $(document).on('click', '#cancelMexicoAlert', function (e) {
    $('#to_country').val(null).trigger('change');
  })
</script>
