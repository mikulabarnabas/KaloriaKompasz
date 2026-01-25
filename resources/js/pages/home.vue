<script setup>
import Button from "primevue/button";
import Card from "primevue/card";

import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';

import Divider from 'primevue/divider';


import Navbar from "../components/navbar.vue";
import Footer from "../components/footer.vue";

const people = [
  {
    id: 1,
    name: "Mikula Barnabás",
    role: "rövid leírás",
    photo: "",
  },
  {
    id: 2,
    name: "Bóta Gergely",
    role: "rövid leírás",
    photo: "",
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
        <div class="text-center font-semibold">{{$t('home_card_first_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home_card_first_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home_cards_button')" severity="secondary" variant="outlined" class="w-full" />
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
        <div class="text-center font-semibold">{{$t('home_card_second_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home_card_second_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home_cards_button')" severity="secondary" variant="outlined" class="w-full" />
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
        <div class="text-center font-semibold">{{$t('home_card_third_title')}}</div>
      </template>

      <template #content>
        <p class="m-0">
          {{$t('home_card_third_content')}}
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button :label="$t('home_cards_button')" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
  </div>

  <h1 class="text-center text-4xl font-semibold tracking-tight py-12">
    {{$t('faq_title')}}
  </h1>

  <Accordion value="0" class="w-3/4  mx-auto">
    <AccordionPanel value="0">
      <AccordionHeader>{{$t('faq_first_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          {{$t('faq_first_answer')}}
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="1">
      <AccordionHeader>{{$t('faq_second_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          {{$t('faq_second_answer')}}
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="2">
      <AccordionHeader>{{$t('faq_third_question')}}</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          {{$t('faq_third_answer')}}
        </p>
      </AccordionContent>
    </AccordionPanel>
  </Accordion>

  <section class="w-full py-12">
    <div class="mx-auto max-w-5xl px-4">
      <h1 class="text-center text-4xl font-semibold tracking-tight">
        {{$t('team_title')}}
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

              <div class="mt-6 text-2xl font-semibold">
                {{ p.name }}
              </div>

              <div class="mt-2 text-sm">
                {{ p.description }}
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </section>

  <Divider />

  <Footer></Footer>
  
</template>

<style scoped></style>