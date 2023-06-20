@extends('layouts.app')

@section('content')
<div >
    <dms-program-stages></dms-program-stages>

</div>
@endsection

@section('script')
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script src="plugins/toastr/toastr.min.js"></script>
@endsection
