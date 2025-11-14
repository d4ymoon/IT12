@extends('layouts.app')
@section('title', 'Edit Product - ATIN Admin')
@push('styles')
<link href="{{ asset('css/page-style.css') }}" rel="stylesheet">
<style>
    .supplier-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    .remove-supplier {
        color: #dc3545;
        cursor: pointer;
    }
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 5px;
    }
</style>
@endpush
@section('content')
    @include('components.alerts')
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <a href="{{ route('products.index') }}" class="text-decoration-none text-dark">
                    <b class="underline">Products</b>
                </a> 
                > Edit Product
            </h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Products
            </a>
        </div>
    </div>

    <!-- Product Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" maxlength="500">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="manufacturer_barcode" class="form-label">Manufacturer Barcode</label>
                            <input type="text" class="form-control" id="manufacturer_barcode" name="manufacturer_barcode" value="{{ old('manufacturer_barcode', $product->manufacturer_barcode) }}" maxlength="20" 
                                placeholder="Scan or type the barcode number here..." inputmode="numeric" pattern="[0-9]{12,20}"
                                title="Scan the physical product's UPC or EAN code, or manually enter the 12- to 20-digit number."
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            @if($product->image_path)
                                <div class="mb-2">
                                    <img src="{{ $product->image_url }}" alt="Current Image" class="image-preview">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="bi bi-currency-dollar me-2"></i>Pricing & Inventory</h5>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                                title="The price at which this product is sold to the customer."  max="9999999.99">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="last_unit_cost" class="form-label">Last Unit Cost <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="last_unit_cost" name="last_unit_cost" value="{{ old('last_unit_cost', $product->last_unit_cost) }}" step="0.01" min="0" required
                                title="The most recent cost at which this product was purchased from a supplier." max="9999999.99">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity_in_stock" class="form-label">Quantity in Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" value="{{ old('quantity_in_stock', $product->quantity_in_stock) }}" min="0" required max="99999">                        
                        </div>

                        <div class="mb-3">
                            <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" min="0" max="99999" required>
                            <div class="form-text">Alert when stock falls below this level</div>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-truck me-2"></i>Suppliers</h5>
                        <div id="suppliers-container">
                            <!-- Suppliers will be populated dynamically -->
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-outline-primary" id="add-supplier">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add Supplier
                            </button>
                            
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                <i class="bi bi-building me-1"></i>
                                Create New Supplier
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Supplier Modal (same as create page) -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="quickSupplierForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSupplierModalLabel">
                            <i class="bi bi-building me-2"></i>
                            Quick Add Supplier
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quick_supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quick_supplier_name" name="supplier_name" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="quick_contact_info" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="quick_contact_info" name="contact_info" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="quick_address" class="form-label">Address</label>
                            <textarea class="form-control" id="quick_address" name="address" rows="3" maxlength="255"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let supplierCount = 0;

        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Add supplier row
        function addSupplierRow(supplierId = '', unitCost = '') {
            supplierCount++;
            const container = document.getElementById('suppliers-container');
            
            const supplierHtml = `
                <div class="supplier-item" id="supplier-${supplierCount}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" name="suppliers[${supplierCount}][id]">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" ${supplierId == {{ $supplier->id }} ? 'selected' : ''}>{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Unit Cost <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="suppliers[${supplierCount}][default_unit_cost]" value="${unitCost}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-danger w-100 remove-supplier" onclick="removeSupplier(${supplierCount})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', supplierHtml);
        }

        // Remove supplier row
        function removeSupplier(id) {
            const element = document.getElementById(`supplier-${id}`);
            if (element) {
                element.remove();
            }
        }

        // Add supplier button
        document.getElementById('add-supplier').addEventListener('click', function() {
            addSupplierRow();
        });

        // Quick add supplier (same as create page)
        document.getElementById('quickSupplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/suppliers', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.createElement('option');
                    select.value = data.supplier.id;
                    select.textContent = data.supplier.supplier_name;
                    
                    document.querySelectorAll('select[name^="suppliers"]').forEach(dropdown => {
                        dropdown.appendChild(select.cloneNode(true));
                    });
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                    modal.hide();
                    this.reset();
                    
                    alert('Supplier added successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding supplier');
            });
        });

        // Load existing suppliers when page loads
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($product->suppliers as $supplier)
                addSupplierRow({{ $supplier->id }}, {{ $supplier->pivot->default_unit_cost }});
            @endforeach
            
            // If no suppliers, add one empty row
            if (supplierCount === 0) {
                addSupplierRow();
            }
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            const noLeadingZeroInputs = ["quantity_in_stock", "reorder_level"];

            noLeadingZeroInputs.forEach(id => {
                const input = document.getElementById(id);

                input.addEventListener("input", function () {
                    // Remove leading zeros, but allow "0"
                    this.value = this.value.replace(/^0+(?=\d)/, '');
                });
            });
        });

    </script>
    @endpush
@endsection