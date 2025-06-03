@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')
    <div class="contents">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                        <h4 class="text-capitalize">Profil Düzenle</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="user-info-tab w-100 bg-white global-shadow radius-xl mb-50">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-4 col-10">
                                        <div class="mt-sm-40 mb-sm-50 mt-20 mb-20">
                                            <div class="user-tab-info-title mb-sm-40 mb-20 text-capitalize">
                                                <h5 class="fw-500">Profil Bilgileri</h5>
                                            </div>
                                            <div class="edit-profile__body">
                                                <div class="form-group mb-25">
                                                    <label for="name1">Ad & Soyad</label>
                                                    <input value="{{ auth()->user()->name }}" name="name" type="text" class="form-control" id="name1" placeholder="Ad & Soyad">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="name1">Kullanıcı Adı</label>
                                                    <input value="{{ auth()->user()->username }}" name="username" type="text" class="form-control" id="name1" placeholder="Kullanıcı Adı">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="name2">Email</label>
                                                    <input value="{{ auth()->user()->email }}" name="email" type="email" class="form-control" id="name2" placeholder="sample@email.com">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="name1">Şifre</label>
                                                    <input name="password" type="password" class="form-control" id="name1" placeholder="Yeni şifre (Değiştirmek istemiyorsanız boş bırakın)">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="phoneNumber5">Telefon Numarası</label>
                                                    <input value="{{ auth()->user()->phone }}" name="phone" type="tel" class="form-control" id="phoneNumber5" placeholder="05559996655">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="name3">Firma adı</label>
                                                    <input value="{{ auth()->user()->company }}" name="company" type="text" class="form-control" id="name3" placeholder="Example">
                                                </div>
                                                <div class="form-group mb-25">
                                                    <label for="phoneNumber2">Website</label>
                                                    <input value="{{ auth()->user()->website }}" name="website" type="url" class="form-control" id="phoneNumber2" placeholder="www.example.com">
                                                </div>
                                                <div class="button-group d-flex pt-sm-25 justify-content-md-end justify-content-start">
                                                    <button type="submit" class="btn btn-primary btn-default btn-squared text-capitalize radius-md shadow2 btn-sm">Kaydet</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection