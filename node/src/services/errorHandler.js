import {capitalizeFirstLetter} from "./textHelper";

export function handleApiError(error) {
    const response = {...error.response}
    const formErrors = []
    const apiMessage = response.data?.title ?? 'Unknown error'
    if (response?.status === 400) {
        if (response.data?.errors) {
            response.data?.errors.forEach(error => {
                const property = Object.keys(error).shift()
                formErrors.push({
                    property: capitalizeFirstLetter(property),
                    message: error[property]
                })
            })
        }
    }
    return {
        apiMessage: apiMessage,
        formErrors: formErrors,
    }
}