<template>
  <v-app>
    <v-main class="gradient">
      <CardCatalog />
    </v-main>
  </v-app>
</template>

<script>
import CardCatalog from "./components/CardCatalog";

export default {
  name: 'App',
  components: {
    CardCatalog,
  },
};
</script>
<style>
.gradient {
  background: #232526;  /* fallback for old browsers */
  background: -webkit-linear-gradient(to right, #414345, #232526);  /* Chrome 10-25, Safari 5.1-6 */
  background: linear-gradient(to right, #414345, #232526); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}
.v-card.v-sheet.theme--dark {
  background-color: black !important;
  border: 1px solid #64FFDA !important
}
</style>
