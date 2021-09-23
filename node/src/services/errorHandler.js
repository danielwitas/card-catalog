import {capitalizeFirstLetter} from "./textHelper";

export function handleApiError(error) {
    const response = {...error.response}
    const formErrors = []
    const apiMessage = response.data?.['hydra:title'] ?? 'Unknown error'
    if (response?.status === 400 || response?.status === 422) {
        if (response.data?.violations) {
            response.data?.violations.forEach(error => {
                formErrors.push({
                    property: capitalizeFirstLetter(error.propertyPath),
                    message: error.message
                })
            })
        }
    }
    return {
        apiMessage: apiMessage,
        formErrors: formErrors,
    }
}