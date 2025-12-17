
<script setup>
import { reactive, ref } from "vue";

import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";

const form = reactive({
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
  acceptTerms: false,
  subscribe: true,
});

const submitted = ref(false);
const loading = ref(false);

const errors = reactive({
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
  acceptTerms: "",
});

const PASSWORD_REGEX =
  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&()[\]])[A-Za-z\d@$!%*?&()[\]]{8,}$/;

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function clearErrors() {
  errors.name = "";
  errors.email = "";
  errors.password = "";
  errors.confirmPassword = "";
  errors.acceptTerms = "";
}

function validate() {
  clearErrors();

  // name validation
  if (!form.name) {
    errors.name = "Full name is required.";
  } else if (form.name.length < 2) {
    errors.name = "Full name must be at least 2 characters long.";
  } else if (form.name.length > 80) {
    errors.name = "Full name is too long.";
  }

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

  // confirm password validation
  if (!form.confirmPassword) {
    errors.confirmPassword = "Please confirm your password.";
  } else if (form.confirmPassword !== form.password) {
    errors.confirmPassword = "Passwords do not match.";
  }

  // terms validation
  if (!form.acceptTerms) {
    errors.acceptTerms = "You must accept the Terms & Privacy Policy.";
  }

  return (
    !errors.name &&
    !errors.email &&
    !errors.password &&
    !errors.confirmPassword &&
    !errors.acceptTerms
  );
}

async function onSubmit() {
  submitted.value = true;

  if (!validate()) return;

  loading.value = true;
  try {
    // Replace with your API call
    await new Promise((r) => setTimeout(r, 900));
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-slate-50 flex items-center justify-center p-4"
  >
    <div class="w-full max-w-md">
      <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="p-6 sm:p-8">
          <h1 class="text-2xl font-semibold text-slate-900">Create account</h1>
          <p class="mt-1 text-sm text-slate-600">
            Fill in your details to get started.
          </p>

          <form class="mt-6 space-y-5" @submit.prevent="onSubmit" novalidate>
            <!-- Full name -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <InputText
                  id="name"
                  v-model.trim="form.name"
                  type="text"
                  autocomplete="name"
                  class="w-full"
                  :invalid="submitted && !!errors.name"
                  aria-describedby="name-help"
                />
                <label for="name">Full name</label>
              </FloatLabel>

              <small
                id="name-help"
                class="block text-xs"
                :class="
                  submitted && errors.name ? 'text-red-600' : 'text-transparent'
                "
              >
                {{ submitted && errors.name ? errors.name : "." }}
              </small>
            </div>

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
                :class="
                  submitted && errors.email ? 'text-red-600' : 'text-transparent'
                "
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
                  autocomplete="new-password"
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
                :class="
                  submitted && errors.password
                    ? 'text-red-600'
                    : 'text-transparent'
                "
              >
                {{ submitted && errors.password ? errors.password : "." }}
              </small>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
              <FloatLabel variant="on">
                <Password
                  id="confirmPassword"
                  v-model="form.confirmPassword"
                  autocomplete="new-password"
                  class="w-full"
                  input-class="w-full"
                  toggle-mask
                  :feedback="false"
                  :invalid="submitted && !!errors.confirmPassword"
                  aria-describedby="confirm-password-help"
                />
                <label for="confirmPassword">Confirm password</label>
              </FloatLabel>

              <small
                id="confirm-password-help"
                class="block text-xs"
                :class="
                  submitted && errors.confirmPassword
                    ? 'text-red-600'
                    : 'text-transparent'
                "
              >
                {{
                  submitted && errors.confirmPassword
                    ? errors.confirmPassword
                    : "."
                }}
              </small>
            </div>

            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <Checkbox
                  input-id="acceptTerms"
                  v-model="form.acceptTerms"
                  :binary="true"
                />
                <label for="acceptTerms" class="text-sm text-slate-700">
                  I agree to the Terms & Privacy Policy
                </label>
              </div>

              <small
                class="block text-xs"
                :class="
                  submitted && errors.acceptTerms
                    ? 'text-red-600'
                    : 'text-transparent'
                "
              >
                {{ submitted && errors.acceptTerms ? errors.acceptTerms : "." }}
              </small>

              <div class="flex items-center gap-2">
                <Checkbox
                  input-id="subscribe"
                  v-model="form.subscribe"
                  :binary="true"
                />
                <label for="subscribe" class="text-sm text-slate-700">
                  Send me product updates (optional)
                </label>
              </div>
            </div>

            <Button
              type="submit"
              label="Create account"
              class="w-full"
              :loading="loading"
            />

            <p class="text-center text-sm text-slate-600">
              Already have an account?
              <a
                href="/login"
                class="font-medium text-indigo-600 hover:text-indigo-700"
              >
                Sign in
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