<script setup>
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Layout from '@/components/Layout.vue'

const form = useForm({
    email: '',
    password: '',
});

const showPassword = ref(false);
const passwordInputType = computed(() => (showPassword.value ? 'text' : 'password'));

const onLogin = () => {
    form.post(route('save.login'));
};
</script>

<template>
    <Layout title="Login" :withChrome="false">
        <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
            <div class="card p-4 shadow" style="min-width: 350px; max-width: 400px; width: 100%;">
                <div class="text-center mb-4">
                    <i class="fas fa-sign-in-alt fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">Login</h3>
                </div>
                <b-form @submit.prevent="onLogin">
                    <b-form-group label="Email" label-for="loginEmail">
                        <b-form-input id="loginEmail" v-model="form.email" type="email" placeholder="Enter email" />
                        <span class="text-danger" v-if="form.errors.email">
                            {{ form.errors.email }}
                        </span>
                    </b-form-group>
                    <b-form-group label="Password" label-for="loginPassword">
                        <div class="position-relative">
                            <b-form-input id="loginPassword" v-model="form.password" :type="passwordInputType"
                                placeholder="Enter password" />
                            <button type="button" class="btn btn-link p-0 position-absolute top-50 translate-middle-y"
                                style="right: 0.75rem;" @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <span class="text-danger" v-if="form.errors.password">
                            {{ form.errors.password }}
                        </span>
                    </b-form-group>
                    <b-button type="submit" variant="primary" :disabled="form.processing"
                        class="w-100 mt-2">Login</b-button>
                </b-form>
            </div>
        </div>
    </Layout>
</template>
