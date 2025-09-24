{{-- This component replaces inline alert divs with modal-based alerts --}}

{{-- Include the message modal component --}}
@include('components.message-modal')

{{-- Hidden div to trigger JavaScript modal display --}}
<div id="hiddenAlerts" style="display: none;">
    @if (session('success'))
        <div data-type="success" data-message="{{ session('success') }}"></div>
    @endif
    
    @if (session('error'))
        <div data-type="error" data-message="{{ session('error') }}"></div>
    @endif
    
    @if (session('warning'))
        <div data-type="warning" data-message="{{ session('warning') }}"></div>
    @endif
    
    @if (session('info'))
        <div data-type="info" data-message="{{ session('info') }}"></div>
    @endif
    
    @if ($errors->any())
        <div data-type="errors" data-messages="{{ json_encode($errors->all()) }}"></div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Process hidden alerts and show as modals
    $('#hiddenAlerts div[data-type]').each(function() {
        const type = $(this).data('type');
        const message = $(this).data('message');
        const messages = $(this).data('messages');
        
        if (type === 'errors' && messages) {
            MessageModal.error(JSON.parse(messages));
        } else if (message) {
            switch(type) {
                case 'success':
                    MessageModal.success(message, { autoDismiss: 4000 });
                    break;
                case 'error':
                    MessageModal.error(message);
                    break;
                case 'warning':
                    MessageModal.warning(message);
                    break;
                case 'info':
                    MessageModal.info(message);
                    break;
            }
        }
    });
});
</script>