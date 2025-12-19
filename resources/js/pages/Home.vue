<script setup>
import Button from "primevue/button";
import Card from "primevue/card";

import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';

import Navbar from "../components/navbar.vue";

const people = [
  {
    id: 1,
    name: "Név 1",
    role: "rövid leírás",
    photo: "",
  },
  {
    id: 2,
    name: "Név 2",
    role: "rövid leírás",
    photo: "",
  }
];

const props = defineProps({
  name: {
    type: String,
    default: "",
  },
});
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
        <div class="text-center font-semibold">Ételnapló</div>
      </template>

      <template #content>
        <p class="m-0">
          Jegyezd fel napi étkezéseidet és kalóriabeviteledet! Az ételnapló segít átlátni a makrotápanyagokat,
          kontrollálni az adagokat, és tudatosabb döntéseket hozni a céljaid eléréséhez.
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Vágjunk bele!" severity="secondary" variant="outlined" class="w-full" />
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
        <div class="text-center font-semibold">Edzésnapló</div>
      </template>

      <template #content>
        <p class="m-0">
          Kövesd nyomon az edzéseidet egyszerűen! Rögzítheted a gyakorlatokat, súlyokat,
          ismétléseket és az edzés időtartamát, így pontos képet kapsz a fejlődésedről és könnyen
          tervezheted a következő edzéseidet.
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Vágjunk bele!" severity="secondary" variant="outlined" class="w-full" />
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
        <div class="text-center font-semibold">Statisztika</div>
      </template>

      <template #content>
        <p class="m-0">
          Vizualizált adatokkal követheted a haladásodat. Grafikonokon láthatod a súlyváltozást, kalóriabevitelt,
          edzésmennyiséget és egyéb fontos mutatókat, hogy könnyebb legyen értékelni az eredményeidet.
        </p>
      </template>

      <template #footer>
        <div class="flex gap-4 mt-1">
          <Button label="Vágjunk bele!" severity="secondary" variant="outlined" class="w-full" />
        </div>
      </template>
    </Card>
  </div>

  <h1 class="text-center text-4xl font-semibold tracking-tight py-12">
    Gyakori kérdések
  </h1>

  <Accordion value="0" class="w-3/4  mx-auto">
    <AccordionPanel value="0">
      <AccordionHeader>Hogyan működik az edzésnapló?</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          Az edzésnapló lehetővé teszi, hogy minden edzésedet részletesen rögzítsd. Felviheted az egyes gyakorlatokat,
          az
          alkalmazott súlyokat, az ismétlések számát, az edzés időtartamát és akár a pihenőidőt is. Minden adat
          automatikusan elmentésre kerül,
          így bármikor visszanézheted a korábbi edzéseidet, elemezheted a fejlődésedet, és könnyebben tervezheted a
          következő edzéseidet. A napló segítségé vel nyomon követheted a hosszú távú trendeket, például az erőnlét és
          állóképesség javulását.
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="1">
      <AccordionHeader>Milyen adatokat mutat a statisztika oldal?</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          A statisztika oldal vizuálisan jeleníti meg a haladásodat. Grafikonokon követheted a testsúlyváltozásodat, a
          napi kalóriabeviteledet, az edzés mennyiségét és más fontos mutatókat,
          például a makrotápanyagok arányát. Ez lehetővé teszi, hogy gyorsan átlásd, mely területeken kell változtatnod
          a
          céljaid eléréséhez.
        </p>
      </AccordionContent>
    </AccordionPanel>
    <AccordionPanel value="2">
      <AccordionHeader>Mit rögzíthetek az ételnaplóban?</AccordionHeader>
      <AccordionContent>
        <p class="m-0">
          Az ételnapló segítségével részletesen nyomon követheted a napi étkezéseidet. Rögzítheted az elfogyasztott
          ételeket, azok mennyiségét, kalóriatartalmát, valamint a makrotápanyagokat (fehérje, szénhidrát, zsír).
          Ez segít abban, hogy tudatosan étkezz, és könnyebben tartsd a napi kalóriakeretedet a céljaid eléréséhez.
        </p>
      </AccordionContent>
    </AccordionPanel>
  </Accordion>

  <section class="w-full py-12">
    <div class="mx-auto max-w-5xl px-4">
      <h1 class="text-center text-4xl font-semibold tracking-tight">
        Csapatunk
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
</template>

<style scoped></style>