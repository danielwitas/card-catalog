<template>
  <v-container fluid>
    <v-img
        src="../assets/logo.png"
        class="my-3"
        contain
        height="200"
    />
    <v-snackbar
        :color="notificationIsSuccess ? 'primary' : 'error'"
        dark
        top
        v-model="notification"
        :timeout="timeout"
        class="text-center"
    >
      <v-container class="text-center pa-0">
        <v-icon class="mr-3">
          {{ notificationIsSuccess ? 'mdi-check-circle-outline' : 'mdi-alert-outline' }}
        </v-icon>
        {{ notificationText }}
      </v-container>
    </v-snackbar>

    <v-container class="d-flex">
      <v-spacer></v-spacer>
      <CardAddModal
          v-on:card-added="handleAddedCard"
          v-on:api-error="handleApiError"
      />
      <v-spacer></v-spacer>
    </v-container>
    <v-container fluid style="width: 1200px;" class="mx-auto">
      <v-row>
        <v-col
            v-for="(card, index) in cards"
            :key="index"
            cols="12"
            md="4"
        >
          <v-card
              flat
              rounded
              elevation="20"
              dark
              width="375"
              height="275"
              class="mx-auto"

          >
            <v-container fill-height fluid style="height: 250px" v-if="loadingData">
              <v-row align="center" justify="center">
                <v-progress-circular
                    :size="70"
                    :width="7"
                    color="teal accent-2"
                    indeterminate
                ></v-progress-circular>
              </v-row>
            </v-container>
            <div v-else>
              <v-list-item three-line class="my-5">
                <v-list-item-content>
                  <v-list-item-title class="text-h3 mb-1">
                    {{ card.name }}
                  </v-list-item-title>
                </v-list-item-content>

                <v-list-item-avatar
                    size="80"
                    class="text-h6"
                    color="teal darken-1"
                    style="flex-basis: 100px"
                >
                  {{ card.power }}
                </v-list-item-avatar>
              </v-list-item>
              <v-divider></v-divider>
              <v-rating
                  class="text-center"
                  empty-icon="mdi-crop-square"
                  full-icon="mdi-square"
                  hover
                  readonly
                  color="teal darken-1"
                  length="10"
                  size="10"
                  background-color="white"
                  :value="parseInt(card.power)"
              ></v-rating>
              <v-divider></v-divider>
              <v-card-actions>
                <CardRemoveModal
                    :card="card"
                    v-on:card-removed="handleRemovedCard"
                    v-on:api-error="handleApiError"
                />
                <v-spacer></v-spacer>
                <CardEditModal
                    :card="card"
                    v-on:card-edited="handleUpdatedCard"
                    v-on:api-error="handleApiError"
                />
              </v-card-actions>
            </div>

          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-container>
      <div class="text-center">
        <v-pagination
            v-if="this.cards.length > 0 && this.totalPages > 1"
            v-model="page"
            :length="totalPages"
            @input="getCardCollection"
        ></v-pagination>
      </div>
    </v-container>


  </v-container>
</template>

<script>
import {getCardsRequest} from "../api/cards";
import CardAddModal from "./CardAddModal";
import CardRemoveModal from "./CardRemoveModal";
import CardEditModal from "./CardEditModal";
import {handleApiError} from "../services/errorHandler";

export default {
  name: "CardCatalog",
  components: {CardEditModal, CardRemoveModal, CardAddModal},
  data() {
    return {
      notificationIsSuccess: true,
      notification: false,
      notificationText: '',
      timeout: 2000,
      page: 1,
      totalPages: 1,
      cards: [],
      loadingData: false,
      itemsPerPage: 3,
      dialog: false,
    }
  },
  methods: {
    async getCardCollection(page = this.page) {
      this.loadingData = true
      const response = await getCardsRequest(page)
          .catch(error => {
            const apiError = handleApiError(error)
            this.displayNotification(apiError.apiMessage, false)
          })
      this.loadingData = false;
      if (response?.status === 200) {
        this.setTotalPages(response.data.totalItemCount)
        this.cards = response.data.items
      }
    },
    async handleAddedCard(card) {
      this.displayNotification('Card added')
      this.notification = true
      if (this.cards.length < 3) {
        this.cards.push(card)
      }
      if (this.cards.length === 3) {
        const response = await getCardsRequest(this.page)
        if (response?.status === 200) {
          this.setTotalPages(response.data.totalItemCount)
        }
      }
    },
    handleRemovedCard(card) {
      this.displayNotification('Card removed')
      const index = this
          .cards
          .map(card => {
            return card.id;
          })
          .indexOf(card.id);
      this.cards.splice(index, 1)
      if (this.totalPages > 1 && this.page === 1 && this.cards.length < 3) {
        this.getCardCollection()
      }
      if (this.cards.length === 0 && this.page !== 1) {
        this.page -= 1
        this.getCardCollection()
      }
    },
    handleUpdatedCard(card) {
      this.displayNotification('Card updated')
      const index = this
          .cards
          .map(card => {
            return card.id;
          })
          .indexOf(card.id);
      this.cards.splice(index, 1, card)
    },
    handleApiError(apiMessage) {
      this.displayNotification(apiMessage, false)
    },
    displayNotification(notificationText, isSuccess = true) {
      this.notificationText = notificationText
      this.notificationIsSuccess = isSuccess
      this.notification = true
    },
    setTotalPages(totalItems) {
      if (totalItems) {
        this.totalPages = Math.ceil(totalItems / this.itemsPerPage)
      } else {
        this.totalPages = 0
      }
    }
  },
  async created() {
    await this.getCardCollection()
  }
}
</script>

<style scoped>

</style>