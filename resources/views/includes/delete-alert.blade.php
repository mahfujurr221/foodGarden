{{-- Delete Confirm Modal --}}
<div class="modal fade show" id="confirm-modal" tabindex="-1" aria-modal="true">
     <div class="modal-dialog modal-sm">
          <div class="modal-content">
               <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">You want to delete ?</h4>
                    <button type="button" class="close" data-dismiss="modal">
                         <span aria-hidden="true">×</span>
                    </button>
               </div>
               <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">No. Back !</button>
                         <button type="submit" class="btn btn-primary">Yes, Delete</button>
                    </div>
               </form>
          </div>
     </div>
</div>

<script>
     $('.delete').click(function(event){
     event.preventDefault();
     var url = $(this).attr("href");

     $("#delete-form").attr('action', url);
       $("#confirm-modal").modal('show');
     });
</script>