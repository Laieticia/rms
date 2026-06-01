@extends('layouts.app')

@section('content')
     <div class="content-page">
     <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add category</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="page-list-category.html" data-toggle="validator">
                            <div class="row">                                
                                <div class="col-md-12">
                                    {{-- <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control image-file" name="pic" accept="image/*">
                                    </div> --}}
                                </div>
                                <div class="col-md-12">                      
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" class="form-control" placeholder="Enter Category Name" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div> 
                                  <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description / Category Details</label>
                                                <textarea class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>                                
                                 
                                <div class="col-md-12">
                                    
                                </div>                                 
                            </div>                            
                            <button type="submit" class="btn btn-primary mr-2">Add category</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
      </div>
@endsection