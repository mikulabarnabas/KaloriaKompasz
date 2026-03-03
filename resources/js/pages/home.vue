<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import Accordion from "@/Components/accordion.vue"
import Button from "@/Components/glowingButton.vue"

const faqItems = [
  {
    question: "Miben más ez, mint a többi app?",
    answer: "Személyesebbek vagyunk. Itt nem egy multi algoritmusai mögé bújsz, hanem egy olyan eszközt kapsz, amit mi is nap mint nap használunk az edzéseinkhez."
  },
  {
    question: "Szinkronizálhatom az órámat?",
    answer: "Igen, folyamatosan dolgozunk az Apple Health és Google Fit integrációkon, hogy az adatok maguktól áramoljanak."
  },
  {
    question: "Ingyenes marad az alkalmazás?",
    answer: "Az alap funkciók (kalória és edzésnapló) örökre ingyenesek maradnak, mert hiszünk abban, hogy az egészség nem lehet pénzkérdés."
  }
];

</script>

<template>

<Navbar></Navbar>
  <h1 class="text-center text-4xl font-semibold tracking-tight py-12">
    Kalória Kompasz
  </h1>

  <div class="grid lg:grid-cols-3 md:grid-cols-3 gap-8 px-12">
    <Card class="h-full">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-bowl-food text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">{{$t('home.card_first_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home.card_first_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home.cards_button')" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
    <Card class="">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-heart text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">{{$t('home.card_second_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home.card_second_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home.cards_button')" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
    <Card class="">
      <template #header>
        <div class="flex justify-center pt-3">
          <i class="fa-solid fa-chart-column text-6xl text-brand"></i>
        </div>
      </template>

      <template #title>
        <div class="text-center font-semibold">{{$t('home.card_third_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home.card_third_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home.cards_button')" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
  </div>

  <h1 class="text-center text-4xl font-semibold tracking-tight py-12">
    {{$t('home.faq_title')}}
  </h1>

  <Accordion value="0" class="w-3/4  mx-auto">
    <AccordionPanel value="0">
      <AccordionHeader>{{$t('home.faq_first_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          {{$t('home.faq_first_answer')}}
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="1">
      <AccordionHeader>{{$t('home.faq_second_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          {{$t('home.faq_second_answer')}}
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="2">
      <AccordionHeader>{{$t('home.faq_third_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          AAAAAAAAAAAAAAAb
        </p>
      </AccordionContent>
    </AccordionPanel>
  </Accordion>

  <section class="w-full py-12">
    <div class="mx-auto max-w-5xl px-4">
      <h1 class="text-center text-4xl font-semibold tracking-tight">
        {{$t('home.team_title')}}
      </h1>

      <div class="mx-auto mt-12 grid max-w-3xl grid-cols-1 gap-12 sm:grid-cols-2">
        <Card v-for="p in people" :key="p.id" class="h-full" :pt="{
          root: { class: 'h-full' },
          body: { class: 'h-full' },
          content: { class: 'h-full' },
        }">
          <template #content>
            <div class="flex h-full flex-col items-center text-center">
              <div class="h-44 w-44 overflow-hidden rounded-full border">
                <img :src="p.photo" :alt="p.name" class="h-full w-full object-cover" loading="lazy" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-4xl mx-auto">

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition">
                  <img alt="Alapító 1"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAAh2wlXleEXK9sxmGaVzhsMJXeaJecyfwhGr_F7aE9qGJ_UehP914nVYc2a1aiCKdAtcBnUhpTT4dltsxwC2jnHCiVvhwywv7PK9V8gK2JtyJwQb-yGr4O8mpVl8Wg7asqN8dTTjlj2i6i_obuvTJxT8hOWwvNPkwIP3NY9QvgnPne6x8LBtO82B5Z5a0MjGT30GUp9A-LxJ1p3Rb78cIKudSLcv2Ym4TH1J1Ib9WCkjRlz3SFV5G7ubOJBTtOpnymvHMX-E8CJeJN" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Név 1</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Fejlesztő</p>
              </div>

              <div class="group flex flex-col items-center text-center">
                <div
                  class="relative mb-6 h-48 w-48 overflow-hidden rounded-full border-4 border-transparent group-hover:border-primary transition p-1">
                  <img alt="Alapító 2"
                    class="h-full w-full rounded-full object-cover grayscale group-hover:grayscale-0 transition"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTxUiUKhhbC_9IvJmhJ9UoRGDnrCtJ2xYHsYKU9706bTHtX6nkHzpPB7EfHDuzWcKfX86OWEhB6hclOjH54mIJncqG-a2lkMiJCTzdYe13XtVayhQCmD4MPdL5TnUits_88rwFbGf8V1V1VkEz9TgMbd98Akx4F71pmKiOHc3DHbGgxkwLfjb_SmukVKsYplyV-PSGvOYplCInAd_RERo2_5MnSRa_00Qeemg10kCvxYcKn2U-IC5fK0JGTIzz-B6VI3_hb5sf_4aa" />
                </div>
                <h4 class="text-xl font-bold text-main-text">Név 2</h4>
                <p class="text-sm font-medium text-primary uppercase tracking-widest">Alapító / Design</p>
              </div>

            </div>
          </div>
        </section>

        <section class="bg-neutral-dark/30 py-24 px-6">
          <div class="mx-auto max-w-3xl">
            <div class="mb-16 text-center">
              <h2 class="text-4xl font-black text-main-text">Gyakori kérdések</h2>
              <p class="mt-4 text-secondary-text">Minden, amit tudni érdemes a platformunkról.</p>
            </div>

            <Accordion :items="faqItems" />

          </div>
        </section>
      </main>
    </div>
  </AppLayout>
</template>

<style scoped></style>