<template>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-8">
                <div class="back-image"
                    :style="'background-image: url(' + urlGenerator(configData.company_banner) + ')'">
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 pl-md-0">
                <div class="login-form d-flex align-items-center">
                    <form class="sign-in-sign-up-form w-100" ref="form" data-url="/user/register" action="store">
                        <div class="text-center mb-4">
                            <img :src="configData.company_logo ?
                                urlGenerator(configData.company_logo) :
                                urlGenerator('/images/core.png')" alt="" class="img-fluid logo">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <h6 class="text-center mb-0">{{ $t('sign_up') }}</h6>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_first_name">Nombre</label>
                                <app-input type="text" v-model="user.first_name" :placeholder="'Ingrese su nombre'"
                                    :required="true" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_last_name">Apellido</label>
                                <app-input type="text" v-model="user.last_name" :placeholder="'Ingrese su apellido'"
                                    :required="true" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_last_name">{{ $t('email') }}</label>
                                <app-input type="email" v-model="user.email" :placeholder="'Ingrese su email'"
                                    :required="true" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_password">Contraseña</label>
                                <app-input type="password" v-model="user.password" :specialValidation="true"
                                    :show-password="true" :placeholder="'Ingrese su contraseña'" :required="true" />
                                <PasswordWarning />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_password_confirmation">Confirma contraseña</label>
                                <app-input type="password" same-as="user_password" v-model="user.password_confirmation"
                                    :show-password="true" :placeholder="'Confirme su contraseña'" :required="true" />
                            </div>

                        </div>
                        <div class="card card-with-shadow border-0 mb-primary">
                            <div class="card-header p-primary bg-transparent">
                                <h5 class="card-title m-0">Cargue su documento de identidad</h5>
                            </div>
                            <app-overlay-loader v-if="preloader" />
                            <div class="form-group mb-primary row align-items-center">
                                <div class="col-sm-12 default-file">
                                    <input type="file" @change="handleFileChange" :label="'Cargue su archivo'" />
                                    <small class="text-muted font-italic">
                                        {{ $t('recommended_default_file_validation') }}
                                    </small>
                                </div>
                            </div>
                            <label for="referral_code">Código de referido</label>
                            <input
                                type="text"
                                id="referral_code"
                                v-model="user.referral_code"
                                class="form-control"
                                placeholder="Ingrese su codigo de invitado"
                            />



                        </div>
                        <div class="form-row" v-if="recaptchaEnable == 1">
                            <div class="form-group col-12 px-0">
                                <re-captcha :site-key="siteKey"></re-captcha>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <app-load-more :preloader="preloader" :label="'Registrarse'" type="submit"
                                    class-name="btn btn-primary btn-block text-center" @submit="submit" />
                            </div>
                        </div>
                        <div
                            class="form-row form-row flex-column flex-md-row justify-content-center justify-content-md-between justify-content-lg-between">
                            <a :href="urlGenerator('/admin/users/login')"
                                class="bluish-text d-flex align-items-center justify-content-center justify-content-lg-end">
                                <app-icon name="log-in" class="pr-2" /> Iniciar sesión
                            </a>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <p class="text-center mt-5">
                                    {{ $t('copyright_text') + configData.company_name }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ThemeMixin from "../../../../core/mixins/global/ThemeMixin";
import PasswordWarning from './PasswordWarning';
import { AuthMixin } from "./Mixins/AuthMixin";
import { urlGenerator } from "../../../Helpers/AxiosHelper";

export default {
    name: "Registration",
    mixins: [AuthMixin, ThemeMixin],
    components: {
        PasswordWarning
    },
    props: {
        siteKey: String,
        recaptchaEnable: {},
    },
    data() {
        return {
            urlGenerator,
            user: {},
            userData: {},
            preloader: false,
            file: null,
            referralCode: null
        }
    },
    created() {
        // Capturar el parámetro referral_code desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('referral_code')) {
            this.user.referral_code = urlParams.get('referral_code');
        }
    },

    methods: {
        handleFileChange(event) {
            this.user.document = event.target.files[0]; // Obtener el archivo seleccionado
        },
        submit() {
            const formData = new FormData();

            // Iterar sobre las propiedades del objeto user
            Object.keys(this.user).forEach((key) => {
                if (key === "document") {
                    formData.append(key, this.user.document); // Adjuntar el archivo
                } else {
                    formData.append(key, this.user[key]); // Adjuntar otras propiedades
                }
            });

            this.save(formData);
        },
        afterSuccess(res) {
            this.$toastr.s(res.data.message);
            setTimeout(() => {
                window.location = urlGenerator('/');
            }, 3000)
            // window.location = urlGenerator('/admin/users/login');
        },
    }

}
</script>
