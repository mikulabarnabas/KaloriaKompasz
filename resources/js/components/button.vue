<script setup>
defineProps({
  href: String,
  variant: { default: "primary" }
})
</script>

<template>
  <a v-if="href" :href="href" :class="classes">
    <slot/>
  </a>

  <button v-else :class="classes">
    <slot/>
  </button>
</template>

<script>
export default {
  computed: {
    classes() {
      return this.variant === "primary"
        ? "px-6 py-3 bg-primary text-background-dark font-bold rounded-xl hover:brightness-110"
        : "px-6 py-3 border border-neutral-border rounded-xl hover:bg-neutral-dark"
    }
  }
}
</script>
