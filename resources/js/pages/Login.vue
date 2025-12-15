<!-- LoginForm.vue (JavaScript, not TypeScript) -->
<script setup>
import { reactive, ref } from "vue";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";

const form = reactive({
  email: "",
  password: "",
  remember: true,
});

const submitted = ref(false);
const loading = ref(false);

const errors = reactive({
  email: "",
  password: "",
});

const PASSWORD_REGEX =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&()[\]])[A-Za-z\d@$!%*?&()[\]]{8,}$/;

const EMAIL_REGEX = 
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function clearErrors() {
  errors.email = "";
  errors.password = "";
}

function validate() {
  clearErrors();

  // email validation
  if (!form.email) {
    errors.email = "Email is required.";
  } else if (form.email.length > 254) {
    errors.email = "Email is too long.";
  } else if (!EMAIL_REGEX.test(form.email)) {
    errors.email = "Please enter a valid email address.";
  }

  // password validation
  if (!form.password) {
    errors.password = "Password is required.";
  } else if (form.password.length < 8) {
    errors.password = "Password must be at least 8 characters long.";
  } else if (!/[a-z]/.test(form.password)) {
    errors.password = "Password must contain at least one lowercase letter.";
  } else if (!/[A-Z]/.test(form.password)) {
    errors.password = "Password must contain at least one uppercase letter.";
  } else if (!/\d/.test(form.password)) {
    errors.password = "Password must contain at least one number.";
  } else if (!/[@$!%*?&\[\]\(\)]/.test(form.password)) {
    errors.password =
      "Password must contain at least one special character: @$!%*?&.";
  } else if (!PASSWORD_REGEX.test(form.password)) {
    errors.password =
      "Password contains invalid characters. Allowed: letters, numbers, @$!%*?&.";
  }

  return !errors.email && !errors.password;
}

async function onSubmit() {
  submitted.value = true;

  if (!validate()) return;

  loading.value = true;
  try {
    await new Promise((r) => setTimeout(r, 800));
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="min-h-screen w-full bg-slate-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold text-slate-900">Sign in</h1>
          <p class="mt-1 text-sm text-slate-600">
            Use your email and password to continue.
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Email -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="email"
                  v-model.trim="form.email"
                  type="email"
                  autocomplete="email"
                  inputmode="email"
                  class="w-full"
                  :invalid="submitted && !!errors.email"
                  aria-describedby="email-help"
                />
                <label for="email">Email</label>
              </FloatLabel>

              <small
                id="email-help"
                class="block text-xs"
                :class="submitted && errors.email ? 'text-red-600' : 'text-transparent'"
              >
                {{ submitted && errors.email ? errors.email : "." }}
              </small>
            </div>

            <!-- Password -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Password
                  id="password"
                  v-model="form.password"
                  autocomplete="current-password"
                  class="w-full"
                  input-class="w-full"
                  toggle-mask
                  :feedback="false"
                  :invalid="submitted && !!errors.password"
                  aria-describedby="password-help"
                />
                <label for="password">Password</label>
              </FloatLabel>

              <small
                id="password-help"
                class="block text-xs"
                :class="submitted && errors.password ? 'text-red-600' : 'text-transparent'"
              >
                {{ submitted && errors.password ? errors.password : "." }}
              </small>
            </div>

            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-2">
                <Checkbox input-id="remember" v-model="form.remember" :binary="true" />
                <label for="remember" class="text-sm text-slate-700">Remember me</label>
              </div>

              <a
                href="#"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-700"
              >
                Forgot password?
              </a>
            </div>

            <Button type="submit" label="Sign in" class="w-full" :loading="loading" />

            <p class="text-center text-sm text-slate-600">
              Don't have an account?
              <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-700">
                Create one
              </a>
            </p>
          </form>
        </div>
      </div>

      <p class="mt-4 text-center text-xs text-slate-500">
        By continuing you agree to our Terms & Privacy Policy.
      </p>
    </div>
  </div>
</template>

<style scoped></style>