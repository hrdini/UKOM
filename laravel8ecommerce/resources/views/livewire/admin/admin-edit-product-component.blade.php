<div>
    <div class="container" style="padding: 30px 0;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                Edit Menu
                            </div>
                            <div class="col-md-6">
                                <a href="{{route('admin.products')}}" class="btn tbn-success pull-right">All Menu</a>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{Session::get('message')}}</div>
                        @endif
                        <form action="" class="form-horizontal" enctype="multipart/form-data" wire:submit.prevent="updateProduct">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Item Name</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="Product Name" class="form-control input-md" wire:model="name" wire:keyup="generateSlug" />
                                    @error('name')  
                                    <p class="text-danger">{{$message}}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Menu Slug</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="Product Slug" class="form-control input-md" wire:model="slug" />
                                    @error('slug')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Short Description</label>
                                <div class="col-md-4" wire:ignore>
                                    <textarea type="text" class="form-control input-md" id="short_description" placeholder="Short Description" wire:model="short_description"></textarea>
                                    @error('short_description')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Description</label>
                                <div class="col-md-4" wire:ignore>
                                    <textarea class="form-control" id="description" placeholder="Description" wire:model="description"></textarea>
                                    @error('description')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Regular Price</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="Regular Price" class="form-control input-md" wire:model="regular_price" />
                                    @error('regular_price')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Sale Price</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="Sale Price" class="form-control input-md" wire:model="sale_price" />
                                    @error('sale_price')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">SKU</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="SKU" class="form-control input-md" wire:model="SKU" />
                                    @error('SKU')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Stock</label>
                                <div class="col-md-4">
                                    <select class="form-control" wire:model="stock_status">
                                        <option value="instock">InStock</option>
                                        <option value="outofstock">Out Of Stock</option>
                                        @error('stock_status')  <p class="text-danger">{{$message}}</p> @enderror
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Featured</label>
                                <div class="col-md-4">
                                    <select class="form-control" wire:model="featured">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                        @error('featured')  <p class="text-danger">{{$message}}</p> @enderror
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Quantity</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="Quantity" class="form-control input-md" wire:model="quantity" />
                                    @error('quantity')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Menu Image</label>
                                <div class="col-md-4">
                                    <input type="file" class="input-file" wire:model="newimage" />
                                    @if($newimage)
                                    <img src="{{$newimage->temporaryUrl()}}" width="120" />
                                    @else
                                    <img src="{{asset('assets/iamges/products')}}/{{$image}}" width="120" />
                                    @endif
                                    @error('newimage')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Menu Gallery</label>
                                <div class="col-md-4">
                                    <input type="file" class="input-file" wire:model="newimages" multiple />
                                    @if($newimages)
                                    @foreach($newimages as $newimage)
                                    @if($newimage)
                                    <img src="{{$newimage->temporaryUrl()}}" width="120" />
                                    @endif
                                    @endforeach
                                    @else
                                    @foreach($images as $image)
                                    @if($image)
                                    <img src="{{asset('assets/iamges/products')}}/{{$image}}" width="120" />
                                    @endif
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Category</label>
                                <div class="col-md-4">
                                    <select class="form-control" wire:model="category_id" wire:change="changeSubcategory">
                                        <option value="">Select category</option>
                                        @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')  <p class="text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Sub Category</label>
                                <div class="col-md-4">
                                    <select class="form-control" wire:model="scategory_id">
                                        <option value="0">Select category</option>
                                        @foreach ($scategories as $scategory)
                                        <option value="{{$scategory->id}}">{{$scategory->name}}</option>
                                        @endforeach
                                        @error('scategory_id')  <p class="text-danger">{{$message}}</p> @enderror
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Menu Attributes</label>
                                <div class="col-md-4">
                                    <select class="form-control" wire:model="attr">
                                        <option value="0">Select Attribute</option>
                                        @foreach ($pattributes as $pattribute)
                                        <option value="{{$pattribute->id}}">{{$pattribute->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                        <button type="button" class="btn btn-info" wire:click.prevent="add">Add</button>
                                </div>
                            </div>

                            @foreach($inputs as $key => $value)
                            <div class="form-group">
                                <label class="col-md-4 control-label">{{$pattributes->where('id',$attribute_arr[$key])->first()->name}}</label>
                                <div class="col-md-4">
                                    <input type="text" placeholder="{{$pattributes->where('id',$attribute_arr[$key])->first()->name}}" class="form-control input-md" wire:model="attribute_values.{{$value}}" />
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm" wire:click.prevent="remove({{$key}})">Remove</button>
                                </div>
                            </div>
                            @endforeach

                            <div class="form-group">
                                <label class="col-md-4 control-label"></label>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function(){
        tinymce.init({
            selector:'#short_description',
            setup:function(editor){
                editor.on('Change',function(e){
                    tinyMCE.triggerSave();
                    var sd_data = $('#short_description').val();
                    @this.set('short_description',sd_data);
                });
            }
        });

        tinymce.init({
            selector:'#description',
            setup:function(editor){
                editor.on('Change',function(e){
                    tinyMCE.triggerSave();
                    var d_data = $('#description').val();
                    @this.set('description',d_data);
                });
            }
        });
    });
</script>
@endpush
