@extends('backend.layouts.society.main')
@section('title')
    Payment Setting
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Payment Setting</h3>
        </div>
        <div class="separator-breadcrumb border-top mb-4"></div>
        <div class="col-md-12 my-4">
            <div>
                <div class="row"> 
                    <div class="col-md-4"> 
                        <div class="card mb-4 position-relative p-3" style="background-color: #D9D8D4">
                            <label for="national">
                                <h5 class="text-dark mt-2">Payment Setting For National</h5>
                                <div class="position-absolute" style="bottom: 40px; right: 20px;">

                                    <input class="form-check-input" type="radio" name="payment" value="national"
                                        id="national" style="transform: scale(2);" checked>
                                </div>
                            </label>
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4 position-relative p-3" style="background-color: #D9D8D4">
                            <label for="international">
                                <h5 class="text-dark mt-2">Payment Setting For International</h5>
                                <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                    <input class="form-check-input" type="radio" name="payment" value="international"
                                        id="international" style="transform: scale(2);">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class=" mb-4">
                <div class="card-body">
                    <form id="paymentForm">
                        <input type="hidden" id="currentNationalTab" value="fonepay">
                        <input type="hidden" id="currentInternationalTab" value="himalayan_bank">
                        <div class="" id="nationalSection">
                            <ul class="nav nav-pills" id="myPillTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#fonePIll" role="tab" aria-controls="fonePIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>FonePay</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#mocoPIll" role="tab" aria-controls="mocoPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Moco</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#esewaPIll" role="tab" aria-controls="esewaPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Esewa</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#khaltiPIll" role="tab" aria-controls="khaltiPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Khalti</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#connectipsPIll" role="tab" aria-controls="connectipsPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>ConnectIPS</a></li>
                                <li class="nav-item"><a class="nav-link" id="profile-icon-pill" data-bs-toggle="pill"
                                        href="#bankPIll" role="tab" aria-controls="bankPIll" aria-selected="false"><i
                                            class="nav-icon i-Home1 mr-1"></i>QR + Account Details</a></li>
                            </ul>
                            <div class="tab-content  mt-4" id="myPillTabContent">
                                <div class="tab-pane fade show active" id="fonePIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="profile_id">Profile Id <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('profile_id') is-invalid @enderror profile_id"
                                                name="profile_id" id="profile_id"
                                                value="{{ $nationalPayment ? $nationalPayment->profile_id : '' }}" />
                                            <div class="text-danger" id="profileIdError"></div>
                                            @error('profile_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="secret_key">Shared Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('secret_key') is-invalid @enderror secret_key"
                                                name="secret_key" id="secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->secret_key : '' }}" />
                                            <div class="text-danger" id="secretKeyError"></div>

                                            @error('secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="mocoPIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_merchant_id">Merchant Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_merchant_id') is-invalid @enderror moco_merchant_id"
                                                name="moco_merchant_id" id="moco_merchant_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_merchant_id : '' }}" />
                                            <div class="text-danger" id="mocoMerchantIdError"></div>
                                            @error('moco_merchant_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_outlet_id">Outlet Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_outlet_id') is-invalid @enderror moco_outlet_id"
                                                name="moco_outlet_id" id="moco_outlet_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_outlet_id : '' }}" />
                                            <div class="text-danger" id="mocoOutletIdError"></div>
                                            @error('moco_outlet_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_terminal_id">Terminal Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_terminal_id') is-invalid @enderror moco_terminal_id"
                                                name="moco_terminal_id" id="moco_terminal_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_terminal_id : '' }}" />
                                            <div class="text-danger" id="mocoTerminalIdError"></div>
                                            @error('moco_terminal_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_shared_key">Shared Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_shared_key') is-invalid @enderror moco_shared_key"
                                                name="moco_shared_key" id="moco_shared_key"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_shared_key : '' }}" />
                                            <div class="text-danger" id="mocoSharedKeyError"></div>
                                            @error('moco_shared_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData" id="submitData">
                                                {{ $nationalPayment ? 'Update' : 'Save' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="esewaPIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="esewa_product_code">Product Code
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('esewa_product_code') is-invalid @enderror esewa_product_code"
                                                name="esewa_product_code" id="esewa_product_code"
                                                value="{{ $nationalPayment ? $nationalPayment->esewa_product_code : '' }}" />
                                            <div class="text-danger" id="productCodeError"></div>
                                            @error('esewa_product_code')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="esewa_secret_key">Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('esewa_secret_key') is-invalid @enderror esewa_secret_key"
                                                name="esewa_secret_key" id="esewa_secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->esewa_secret_key : '' }}" />
                                            <div class="text-danger" id="esewaSecretKeyError"></div>

                                            @error('esewa_secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="khaltiPIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="khalti_live_secret_key">Live Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                placeholder="example: live_secret_key_68791341fdd94846a146f0457ff7b455"
                                                class="form-control @error('khalti_live_secret_key') is-invalid @enderror khalti_live_secret_key"
                                                name="khalti_live_secret_key" id="khalti_live_secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->khalti_live_secret_key : '' }}" />
                                            <div class="text-danger" id="khaltiLiveSecretKeyError"></div>

                                            @error('khalti_live_secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="connectipsPIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">

                                        <div class="col-md-12 mb-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>ConnectIPS Integration Requirements:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Obtain credentials from ConnectIPS (Merchant ID, App ID, App Name, Password)</li>
                                                    <li>Digital certificate (PFX file) required for token signing - contact ConnectIPS</li>
                                                    <li>Configure callback URLs in conference registration payment section</li>
                                                    <li>Certificate should be placed in: <code>storage/certificates/connectips/{{ $society->id }}/</code></li>
                                                    <li><strong>UAT Server:</strong> https://uat.connectips.com:7443</li>
                                                    <li><strong>Production Server:</strong> https://connectips.com:7443 (when live)</li>
                                                </ul>
                                            </div>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Network Requirements:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Your server IP must be whitelisted by ConnectIPS</li>
                                                    <li>Port 7443 must be open in your firewall</li>
                                                    <li>If UAT server is unavailable, contact ConnectIPS technical support</li>
                                                    <li>Test certificate path: <code>storage/certificates/connectips/{{ $society->id }}/private_key.pem</code></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="connectips_merchant_id">Merchant Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control"
                                                name="connectips_merchant_id"
                                                id="connectips_merchant_id"
                                                placeholder="e.g., 3185"
                                                value="{{ $nationalPayment ? $nationalPayment->connectips_merchant_id : '' }}" />
                                            <div class="text-danger" id="connectipsMerchantIdError"></div>
                                            @error('connectips_merchant_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="connectips_app_id">App Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control"
                                                name="connectips_app_id"
                                                id="connectips_app_id"
                                                placeholder="e.g., MER-3185-APP-1"
                                                value="{{ $nationalPayment ? $nationalPayment->connectips_app_id : '' }}" />
                                            <div class="text-danger" id="connectipsAppIdError"></div>
                                            @error('connectips_app_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="connectips_app_name">App Name
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control"
                                                name="connectips_app_name"
                                                id="connectips_app_name"
                                                placeholder="e.g., Your Society Name"
                                                value="{{ $nationalPayment ? $nationalPayment->connectips_app_name : '' }}" />
                                            <div class="text-danger" id="connectipsAppNameError"></div>
                                            @error('connectips_app_name')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="connectips_password">Password (Basic Auth)
                                                <code>*</code></label>
                                            <input type="password"
                                                class="form-control"
                                                name="connectips_password"
                                                id="connectips_password"
                                                placeholder="Enter ConnectIPS password"
                                                value="{{ $nationalPayment ? $nationalPayment->connectips_password : '' }}" />
                                            <div class="text-danger" id="connectipsPasswordError"></div>
                                            @error('connectips_password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData" id="submitData">
                                                <i class="fas fa-save"></i> {{ $nationalPayment ? 'Update' : 'Save' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bankPIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    <input type="hidden" name="id" id="id"
                                        value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                    <div class="mb-6">
                                        <textarea class="form-control ckeditor" id="national_bank_detail" name="national_bank_detail" rows="5" cols="30">{{ !empty(old('national_bank_detail')) ? old('national_bank_detail') : $nationalPayment?->account_detail }}</textarea>
                                        <div class="text-danger" id="nationalBankDetailError"></div>
                                        @error('national_bank_detail')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-12" style="display: flex; justify-content: end;">
                                        <button type="submit" class="btn btn-primary submitData"
                                            id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="" id="internationalSection" style="display: none;">
                            <ul class="nav nav-pills" id="myPillTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="home-icon-pill"
                                        data-bs-toggle="pill" href="#homePIll" role="tab" aria-controls="homePIll"
                                        aria-selected="true"><i class="nav-icon i-Home1 mr-1"></i>Himalayan Bank</a></li>
                                <li class="nav-item"><a class="nav-link" id="static-qr-pill" data-bs-toggle="pill"
                                        href="#staticQrPill" role="tab" aria-controls="staticQrPill"
                                        aria-selected="false"><i class="nav-icon i-Home1 mr-1"></i>Static QR</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" id="profile-icon-pill" data-bs-toggle="pill"
                                        href="#profilePIll" role="tab" aria-controls="profilePIll"
                                        aria-selected="false"><i class="nav-icon i-Home1 mr-1"></i>Account Details</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-4" id="myPillTabContent">
                                <div class="tab-pane fade show active" id="homePIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        <input type="hidden" name="international_id" id="international_id"
                                            value="{{ $internationalPayment ? $internationalPayment->id : '' }}">
                                        
                                        <!-- Country Selection Section -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-3">
                                                        <i class="fas fa-globe"></i> Country Selection for Payment Gateway
                                                    </h5>
                                                    <p class="text-muted small mb-3">
                                                        Select which countries can use Himalayan Bank payment gateway. You can select all countries or specific countries.
                                                    </p>
                                                    
                                                    <div class="form-group mb-3">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="selectAllCountries">
                                                            <label class="custom-control-label" for="selectAllCountries">
                                                                <strong>Select All Countries</strong>
                                                            </label>
                                                        </div> 
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">
                                                            Available Countries <code>*</code>
                                                            <span class="badge badge-info ml-2" id="selectedCountriesCount">0 selected</span>
                                                        </label>
                                                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background-color: #f8f9fa;">
                                                            <div class="row">
                                                                @foreach($countries as $country)
                                                                    <div class="col-md-4 mb-2">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" 
                                                                                   class="custom-control-input country-checkbox" 
                                                                                   name="selected_countries[]" 
                                                                                   id="country_{{ $country->id }}" 
                                                                                   value="{{ $country->id }}"
                                                                                   {{ in_array($country->id, $selectedCountries) ? 'checked' : '' }}>
                                                                            <label class="custom-control-label" for="country_{{ $country->id }}">
                                                                                {{ $country->country_name }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="text-danger mt-2" id="countriesError"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="merchant_key">Merchant Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('merchant_key') is-invalid @enderror merchant_key"
                                                name="merchant_key" id="merchant_key"
                                                value="{{ $internationalPayment ? $internationalPayment->merchant_key : '' }}" />
                                            <div class="text-danger" id="merchantKeyError"></div>
                                            @error('merchant_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="api_key">Api Key <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('api_key') is-invalid @enderror api_key"
                                                name="api_key" id="api_key"
                                                value="{{ $internationalPayment ? $internationalPayment->api_key : '' }}" />
                                            <div class="text-danger" id="apiKeyError"></div>
                                            @error('api_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="access_token">AccessToken
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('access_token') is-invalid @enderror access_token"
                                                name="access_token" id="access_token"
                                                value="{{ $internationalPayment ? $internationalPayment->access_token : '' }}" />
                                            <div class="text-danger" id="accessTokenError"></div>
                                            @error('access_token')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="encryption_key_id">EncryptionKeyId
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('encryption_key_id') is-invalid @enderror encryption_key_id"
                                                name="encryption_key_id" id="encryption_key_id"
                                                value="{{ $internationalPayment ? $internationalPayment->encryption_key_id : '' }}" />
                                            <div class="text-danger" id="encryptionKeyIdError"></div>
                                            @error('encryption_key_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="merchant_signing_private_key">MerchantSigningPrivateKey
                                                <code>*</code></label>
                                            <textarea
                                                class="form-control @error('merchant_signing_private_key') is-invalid @enderror merchant_signing_private_key"
                                                name="merchant_signing_private_key" id="merchant_signing_private_key" rows="8">{{ $internationalPayment ? $internationalPayment->merchant_signing_private_key : '' }}</textarea>

                                            <div class="text-danger" id="merchantSigningPrivateKeyError"></div>
                                            @error('merchant_signing_private_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="paco_encryption_public_key">PacoEncryptionPublicKey
                                                <code>*</code></label>
                                            <textarea class="form-control @error('paco_encryption_public_key') is-invalid @enderror paco_encryption_public_key"
                                                name="paco_encryption_public_key" id="paco_encryption_public_key" rows="8">{{ $internationalPayment ? $internationalPayment->paco_encryption_public_key : '' }}</textarea>
                                            <div class="text-danger" id="pacoEncryptionPublicKeyError"></div>
                                            @error('paco_encryption_public_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="merchant_decryption_private_key">MerchantDecryptionPrivateKey
                                                <code>*</code></label>
                                            <textarea
                                                class="form-control @error('merchant_decryption_private_key') is-invalid @enderror merchant_decryption_private_key"
                                                name="merchant_decryption_private_key" id="merchant_decryption_private_key" rows="8">{{ $internationalPayment ? $internationalPayment->merchant_decryption_private_key : '' }}</textarea>
                                            <div class="text-danger" id="merchantDecryptionPrivateKeyError"></div>
                                            @error('merchant_decryption_private_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="paco_signing_public_key">PacoSigningPublicKey
                                                <code>*</code></label>
                                            <textarea class="form-control @error('paco_signing_public_key') is-invalid @enderror paco_signing_public_key"
                                                name="paco_signing_public_key" id="paco_signing_public_key" rows="8">{{ $internationalPayment ? $internationalPayment->paco_signing_public_key : '' }}</textarea>
                                            <div class="text-danger" id="pacoSigningPublicKeyError"></div>
                                            @error('paco_signing_public_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>


                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $internationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>

                                </div>
                                
                                <!-- Static QR Tab -->
                                <div class="tab-pane fade" id="staticQrPill" role="tabpanel"
                                    aria-labelledby="static-qr-pill">
                                    <div class="row">
                                        <input type="hidden" name="international_id" id="international_id_static_qr"
                                            value="{{ $staticQrPayment ? $staticQrPayment->id : '' }}">
                                        
                                        <!-- Country Selection Section -->
                                        <div class="col-md-12 mb-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-3">
                                                        <i class="fas fa-globe"></i> Country Selection for Static QR Payment
                                                    </h5>
                                                    <p class="text-muted small mb-3">
                                                        Select which countries can use Static QR payment method. You can select all countries or specific countries.
                                                    </p>
                                                    
                                                    <div class="form-group mb-3">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="selectAllCountriesStaticQr">
                                                            <label class="custom-control-label" for="selectAllCountriesStaticQr">
                                                                <strong>Select All Countries</strong>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label">
                                                            Available Countries <code>*</code>
                                                            <span class="badge badge-info ml-2" id="selectedCountriesCountStaticQr">0 selected</span>
                                                        </label>
                                                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background-color: #f8f9fa;">
                                                            <div class="row">
                                                                @foreach($countries as $country)
                                                                    <div class="col-md-4 mb-2">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox" 
                                                                                   class="custom-control-input country-checkbox-static-qr" 
                                                                                   name="selected_countries_static_qr[]" 
                                                                                   id="country_static_qr_{{ $country->id }}" 
                                                                                   value="{{ $country->id }}"
                                                                                   {{ isset($staticQrSelectedCountries) && in_array($country->id, $staticQrSelectedCountries) ? 'checked' : '' }}>
                                                                            <label class="custom-control-label" for="country_static_qr_{{ $country->id }}">
                                                                                {{ $country->country_name }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="text-danger mt-2" id="countriesErrorStaticQr"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- QR Code Details -->
                                        <div class="col-md-12 form-group mb-3">
                                            <label class="form-label" for="static_qr_details">
                                                <i class="fas fa-qrcode"></i> QR Code Details & Instructions
                                                <code>*</code>
                                            </label>
                                            <p class="text-muted small">
                                                Enter payment instructions and QR code details. You can include images, text, and formatting.
                                            </p>
                                            <textarea class="form-control ckeditor" id="static_qr_details" name="static_qr_details" rows="8">{{ $staticQrPayment ? $staticQrPayment->qr_details : '' }}</textarea>
                                            <div class="text-danger mt-2" id="staticQrDetailsError"></div>
                                            @error('static_qr_details')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitDataStaticQr">{{ $staticQrPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="profilePIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    <input type="hidden" name="international_id" id="international_id"
                                        value="{{ $internationalPayment ? $internationalPayment->id : '' }}">
                                    <div class="mb-6">
                                        <textarea class="form-control ckeditor" id="bank_detail" name="bank_detail" rows="5" cols="30">{{ !empty(old('bank_detail')) ? old('bank_detail') : $internationalPayment?->bank_detail }}</textarea>
                                        <div class="text-danger" id="bankDetailError"></div>
                                        @error('bank_detail')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-12" style="display: flex; justify-content: end;">
                                        <button type="submit" class="btn btn-primary submitData"
                                            id="submitData">{{ $internationalPayment ? 'Update' : 'Save' }}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#openModal").modal('show');

            // Country selection functionality
            function updateSelectedCount() {
                let count = $('.country-checkbox:checked').length;
                $('#selectedCountriesCount').text(count + ' selected');
                
                // Update select all checkbox state
                let totalCountries = $('.country-checkbox').length;
                $('#selectAllCountries').prop('checked', count === totalCountries);
            }

            // Initialize count on page load
            updateSelectedCount();

            // Select/Deselect all countries
            $('#selectAllCountries').change(function() {
                let isChecked = $(this).prop('checked');
                $('.country-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Update count when individual checkbox is changed
            $('.country-checkbox').change(function() {
                updateSelectedCount();
            });

            // Country selection functionality for Static QR
            function updateSelectedCountStaticQr() {
                let count = $('.country-checkbox-static-qr:checked').length;
                $('#selectedCountriesCountStaticQr').text(count + ' selected');
                
                // Update select all checkbox state
                let totalCountries = $('.country-checkbox-static-qr').length;
                $('#selectAllCountriesStaticQr').prop('checked', count === totalCountries);
            }

            // Initialize count on page load for Static QR
            updateSelectedCountStaticQr();

            // Select/Deselect all countries for Static QR
            $('#selectAllCountriesStaticQr').change(function() {
                let isChecked = $(this).prop('checked');
                $('.country-checkbox-static-qr').prop('checked', isChecked);
                updateSelectedCountStaticQr();
            });

            // Update count when individual checkbox is changed for Static QR
            $('.country-checkbox-static-qr').change(function() {
                updateSelectedCountStaticQr();
            });

            // Update hidden inputs when tabs change
            $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr('href');

                // National tabs
                if (target === '#fonePIll') {
                    $('#currentNationalTab').val('fonepay');
                } else if (target === '#mocoPIll') {
                    $('#currentNationalTab').val('moco');
                } else if (target === '#esewaPIll') {
                    $('#currentNationalTab').val('esewa');
                } else if (target === '#khaltiPIll') {
                    $('#currentNationalTab').val('khalti');
                } else if (target === '#connectipsPIll') {
                    $('#currentNationalTab').val('connectips');
                } else if (target === '#bankPIll') {
                    $('#currentNationalTab').val('account_details');
                }

                // International tabs
                else if (target === '#homePIll') {
                    $('#currentInternationalTab').val('himalayan_bank');
                } else if (target === '#staticQrPill') {
                    $('#currentInternationalTab').val('static_qr');
                } else if (target === '#profilePIll') {
                    $('#currentInternationalTab').val('account_details');
                }

                console.log('Tab changed - National:', $('#currentNationalTab').val(), 'International:', $(
                    '#currentInternationalTab').val());
            });

            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                    (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                        105)) {
                    return;
                } else {
                    event.preventDefault();
                }
            });

            $('input[name="payment"]').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue == 'national') {
                    $('#nationalSection').show();
                    $('#internationalSection').hide();
                }
                if (selectedValue == 'international') {
                    $('#nationalSection').hide();
                    $('#internationalSection').show();
                }
            });

            $('.submitData').click(function(event) {
                event.preventDefault();

                let isValid = true;
                let activeSection = $('#nationalSection').is(':visible') ? 'national' : 'international';
                let activeTab = '';

                $('.text-danger').text('');

                // Get active tab from hidden inputs
                if (activeSection === 'national') {
                    activeTab = $('#currentNationalTab').val();
                } else if (activeSection === 'international') {
                    activeTab = $('#currentInternationalTab').val();
                }
                console.log('Submitting - Section:', activeSection, 'Tab:', activeTab);

                if (activeSection === 'national') {
                    if (activeTab === 'fonepay') {
                        let profileId = $('#profile_id').val().trim();
                        let secretKey = $('#secret_key').val().trim();

                        if (!profileId) {
                            isValid = false;
                            $('#profileIdError').text('Profile Id is required.');
                        }
                        if (!secretKey) {
                            isValid = false;
                            $('#secretKeyError').text('Shared Secret Key is required.');
                        }
                    } else if (activeTab === 'moco') {
                        let mocoMerchantId = $('#moco_merchant_id').val().trim();
                        let mocoOutletId = $('#moco_outlet_id').val().trim();
                        let mocoTerminalId = $('#moco_terminal_id').val().trim();
                        let mocoSharedKey = $('#moco_shared_key').val().trim();

                        if (!mocoMerchantId) {
                            isValid = false;
                            $('#mocoMerchantIdError').text('Merchant Id is required.');
                        }
                        if (!mocoOutletId) {
                            isValid = false;
                            $('#mocoOutletIdError').text('Outlet Id is required.');
                        }
                        if (!mocoTerminalId) {
                            isValid = false;
                            $('#mocoTerminalIdError').text('Terminal Id is required.');
                        }
                        if (!mocoSharedKey) {
                            isValid = false;
                            $('#mocoSharedKeyError').text('Shared Key is required.');
                        }
                    } else if (activeTab === 'esewa') {
                        let esewaProductCodeId = $('#esewa_product_code').val().trim();
                        let esewaSecretKeyId = $('#esewa_secret_key').val().trim();

                        if (!esewaProductCodeId) {
                            isValid = false;
                            $('#productCodeError').text('Product Code is required.');
                        }
                        if (!esewaSecretKeyId) {
                            isValid = false;
                            $('#esewaSecretKeyError').text('Secret Key is required.');
                        }
                    } else if (activeTab === 'khalti') {
                        let khaltiLiveSecretKeyId = $('#khalti_live_secret_key').val().trim();

                        if (!khaltiLiveSecretKeyId) {
                            isValid = false;
                            $('#khaltiLiveSecretKeyError').text('Live Secret Key is required.');
                        }
                    } else if (activeTab === 'account_details') {
                        if (CKEDITOR.instances['national_bank_detail']) {
                            CKEDITOR.instances['national_bank_detail'].updateElement();
                        }

                        let nationalBankDetailId = $('#national_bank_detail').val().trim();
                        console.log(nationalBankDetailId);
                        if (!nationalBankDetailId) {
                            isValid = false;
                            $('#nationalBankDetailError').text('Bank Detail is required.');
                        }
                    } else if (activeTab === 'connectips') {
                        let connectipsMerchantId = $('#connectips_merchant_id').val().trim();
                        let connectipsAppId = $('#connectips_app_id').val().trim();
                        let connectipsAppName = $('#connectips_app_name').val().trim();
                        let connectipsPassword = $('#connectips_password').val().trim();

                        if (!connectipsMerchantId) {
                            isValid = false;
                            $('#connectipsMerchantIdError').text('Merchant Id is required.');
                        }
                        if (!connectipsAppId) {
                            isValid = false;
                            $('#connectipsAppIdError').text('App Id is required.');
                        }
                        if (!connectipsAppName) {
                            isValid = false;
                            $('#connectipsAppNameError').text('App Name is required.');
                        }
                        if (!connectipsPassword) {
                            isValid = false;
                            $('#connectipsPasswordError').text('Password is required.');
                        }
                    }
                }

                if (activeSection === 'international') {
                    if (activeTab === 'himalayan_bank') {
                        let merchantKey = $('#merchant_key').val().trim();
                        let apiKey = $('#api_key').val().trim();
                        let accessToken = $('#access_token').val().trim();
                        let encryptionKeyId = $('#encryption_key_id').val().trim();
                        let merchantSigningPrivateKey = $('#merchant_signing_private_key').val().trim();
                        let pacoEncryptionPublicKey = $('#paco_encryption_public_key').val().trim();
                        let merchantDecryptionPrivateKey = $('#merchant_decryption_private_key').val()
                            .trim();
                        let pacoSigningPublicKey = $('#paco_signing_public_key').val().trim();
                        let selectedCountries = [];
                        $('.country-checkbox:checked').each(function() {
                            selectedCountries.push($(this).val());
                        });

                        if (selectedCountries.length === 0) {
                            isValid = false;
                            $('#countriesError').text('Please select at least one country.');
                        }
                        if (!merchantKey) {
                            isValid = false;
                            $('#merchantKeyError').text('Merchant Key is required.');
                        }
                        if (!apiKey) {
                            isValid = false; 
                            $('#apiKeyError').text('API Key is required.');
                        }
                        if (!accessToken) {
                            isValid = false;
                            $('#accessTokenError').text('AccessToken is required.');
                        }
                        if (!encryptionKeyId) {
                            isValid = false;
                            $('#encryptionKeyIdError').text('EncryptionKeyId is required.');
                        }
                        if (!merchantSigningPrivateKey) {
                            isValid = false;
                            $('#merchantSigningPrivateKeyError').text(
                                'MerchantSigningPrivateKey is required.');
                        }
                        if (!pacoEncryptionPublicKey) {
                            isValid = false;
                            $('#pacoEncryptionPublicKeyError').text('PacoEncryptionPublicKey is required.');
                        }
                        if (!merchantDecryptionPrivateKey) {
                            isValid = false;
                            $('#merchantDecryptionPrivateKeyError').text(
                                'MerchantDecryptionPrivateKey is required.');
                        }
                        if (!pacoSigningPublicKey) {
                            isValid = false;
                            $('#pacoSigningPublicKeyError').text('PacoSigningPublicKey is required.');
                        }
                    } else if (activeTab === 'static_qr') {
                        let selectedCountriesStaticQr = [];
                        $('.country-checkbox-static-qr:checked').each(function() {
                            selectedCountriesStaticQr.push($(this).val());
                        });

                        if (selectedCountriesStaticQr.length === 0) {
                            isValid = false;
                            $('#countriesErrorStaticQr').text('Please select at least one country.');
                        }

                        // Handle CKEditor content
                        if (CKEDITOR.instances['static_qr_details']) {
                            CKEDITOR.instances['static_qr_details'].updateElement();
                        }

                        let staticQrDetails = $('#static_qr_details').val().trim();
                        if (!staticQrDetails) {
                            isValid = false;
                            $('#staticQrDetailsError').text('QR Code Details are required.');
                        }
                    } else if (activeTab === 'account_details') {
                        if (CKEDITOR.instances['bank_detail']) {
                            CKEDITOR.instances['bank_detail'].updateElement();
                        }

                        let bankDetailId = $('#bank_detail').val().trim();
                        console.log(bankDetailId);
                        if (!bankDetailId) {
                            isValid = false;
                            $('#bankDetailError').text('Bank Detail is required.');
                        }
                    }
                }

                if (isValid) {
                    let formData = {
                        section: activeSection,
                        active_tab: activeTab,
                        // FonePay fields
                        profile_id: $('#profile_id').val(),
                        secret_key: $('#secret_key').val(),
                        // Moco fields
                        moco_merchant_id: $('#moco_merchant_id').val(),
                        moco_outlet_id: $('#moco_outlet_id').val(),
                        moco_terminal_id: $('#moco_terminal_id').val(),
                        moco_shared_key: $('#moco_shared_key').val(),
                        //Esewa Field
                        esewa_product_code: $('#esewa_product_code').val(),
                        esewa_secret_key: $('#esewa_secret_key').val(),
                        //Khalti Field
                        khalti_live_secret_key: $('#khalti_live_secret_key').val(),
                        //ConnectIPS Fields
                        connectips_merchant_id: $('#connectips_merchant_id').val(),
                        connectips_app_id: $('#connectips_app_id').val(),
                        connectips_app_name: $('#connectips_app_name').val(),
                        connectips_password: $('#connectips_password').val(),
                        //National Bank Detail
                        national_bank_detail: $('#national_bank_detail').val(),
                        // Himalayan Bank fields 
                        merchant_key: $('#merchant_key').val(),
                        api_key: $('#api_key').val(),
                        access_token: $('#access_token').val(),
                        merchant_signing_private_key: $('#merchant_signing_private_key').val(),
                        paco_encryption_public_key: $('#paco_encryption_public_key').val(),
                        merchant_decryption_private_key: $('#merchant_decryption_private_key').val(),
                        paco_signing_public_key: $('#paco_signing_public_key').val(),
                        encryption_key_id: $('#encryption_key_id').val(),
                        //bank_detail 
                        bank_detail: $('#bank_detail').val(),
                        // Static QR fields
                        static_qr_details: $('#static_qr_details').val(),
                        // IDs for updates
                        id: $('#id').val(),
                        international_id: $('#international_id').val(),
                    };

                    // Add selected countries for Himalayan Bank
                    if (activeSection === 'international' && activeTab === 'himalayan_bank') {
                        formData.selected_countries = [];
                        $('.country-checkbox:checked').each(function() {
                            formData.selected_countries.push($(this).val());
                        });
                    }

                    // Add selected countries for Static QR
                    if (activeSection === 'international' && activeTab === 'static_qr') {
                        formData.selected_countries_static_qr = [];
                        $('.country-checkbox-static-qr:checked').each(function() {
                            formData.selected_countries_static_qr.push($(this).val());
                        });
                        formData.international_id = $('#international_id_static_qr').val();
                    }

                    console.log('Form data being sent:', formData);

                    $.ajax({
                        url: '{{ route('payment.setting.submit', $society) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            notyf.success(response.message);
                        },
                        error: function(error) {
                            console.log('Error:', error);
                            if (error.responseJSON && error.responseJSON.errors) {
                                // Handle validation errors
                                $.each(error.responseJSON.errors, function(key, value) {
                                    console.log('Validation error:', key, value);
                                });
                            }
                            notyf.error('There was an error submitting the form.');
                        },
                    });
                }
            });
        });
    </script>
@endsection
