import axios from "axios";

export function getCardsRequest(page) {
    return axios.get(`cards?page=${page}`)
}

export function addCardRequest(card) {
    return axios
        .post(`cards`, {
            name: card.name,
            power: card.power
        })
}

export function removeCardRequest(id) {
    return axios
        .delete(`cards/${id}`)
}

export function editCardRequest(card) {
    return axios
        .put(`cards/${card.id}`, {
            name: card.name,
            power: card.power,
        })
}