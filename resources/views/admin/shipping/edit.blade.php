 @extends('admin.layouts.app')

 @section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
         <div class="container-fluid my-2">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1>Edit Shipping Management</h1>
                 </div>
                 <div class="col-sm-6 text-right">
                     <a href="{{ route('shipping.create') }}" class="btn btn-primary">Back</a>
                 </div>
             </div>
         </div>
         <!-- /.container-fluid -->
     </section>
     <!-- Main content -->
     <section class="content">
         <!-- Default box -->
         <div class="container-fluid">
            @include('admin.message')
             <form action="" method="POST" id="shippingForm">
                 <div class="card">
                     <div class="card-body">
                         <div class="row">
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="name">Location</label>
                                     <input value="{{ !empty($shippingCharge->location) ? $shippingCharge->location : '' }}" type="text" name="location" id="location" class="form-control"
                                         placeholder="Location">
                                     <p class="invalid-feedback"></p>
                                 </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="name">Amount</label>
                                     <input value="{{ !empty($shippingCharge->amount) ? $shippingCharge->amount : '' }}" type="number" name="amount" id="amount" class="form-control"
                                         placeholder="Amount">
                                     <p class="invalid-feedback"></p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="pb-5 pt-3">
                     <button type="submit" class="btn btn-primary">Update</button>
                     <a href="{{ route('shipping.create') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                 </div>
             </form>
         </div>
         <!-- /.card -->
     </section>
     <!-- /.content -->
 @endsection

 @section('customJs')
     <script>
         $("#shippingForm").submit(function(event) {
             event.preventDefault();
             var element = $(this);
             $("button[type=submit]").prop('disabled', true);
             $.ajax({
                 url: '{{ route('shipping.update', $shippingCharge->id) }}',
                 type: 'put',
                 data: element.serializeArray(),
                 dataType: 'json',
                 success: function(response) {

                     $("button[type=submit]").prop('disabled', false);


                     if (response["status"] == true) {

                         window.location.href = "{{ route('shipping.create') }}";

                         $("#location").removeClass('is-invalid')
                             .siblings('.invalid-feedback').html('');

                         $("#amount").removeClass('is-invalid')
                             .siblings('.invalid-feedback').html('');

                     } else {
                         var errors = response['errors'];
                         if (errors['location']) {
                             $("#location").addClass('is-invalid')
                                 .siblings('.invalid-feedback').html(errors['location']);
                         } else {
                             $("#location").removeClass('is-invalid')
                                 .siblings('.invalid-feedback').html('');
                         }

                         if (errors['amount']) {
                             $("#amount").addClass('is-invalid')
                                 .siblings('.invalid-feedback').html(errors['amount']);
                         } else {
                             $("#amount").removeClass('is-invalid')
                                 .siblings('.invalid-feedback').html('');
                         }

                     }



                 },
                 error: function(jqXHR, exception) {
                     console.log("Something went wrong");
                 }
             })
         });
     </script>
 @endsection
