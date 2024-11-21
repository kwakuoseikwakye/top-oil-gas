/**
 * Handles form submission with confirmation modal and API interaction
 * @param {string} formId - ID of the form element (without # prefix)
 * @param {string} modalId - ID of the modal to hide after success (without # prefix)
 * @param {string} apiRoute - API endpoint for form submission
 * @param {string} promptMessage - Confirmation message to show user
 * @param {Function} onSuccess - Callback function to execute on successful submission
 * @param {Object} tableRef - DataTable reference for reloading (optional)
 * @returns {void}
 */
export const handlePrompt = (
      formId,
      modalId,
      apiRoute,
      promptMessage,
      onSuccess = () => { },
      table = null
) => {
      const formElement = document.getElementById(formId);
      const modalElement = document.getElementById(modalId);

      if (!formElement || !modalElement) {
            console.error('Form or modal element not found');
            return;
      }

      const modal = new bootstrap.Modal(modalElement);
      const datatable = table ? $(table).DataTable() : null;

      // Remove any existing event listener to prevent duplicates
      const submitHandler = async (e) => {
            e.preventDefault();

            try {
                  const confirmResult = await Swal.fire({
                        title: promptMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Submit'
                  });

                  if (!confirmResult.isConfirmed) return;

                  // await Swal.fire({
                  //       text: "Processing...",
                  //       showConfirmButton: false,
                  //       allowEscapeKey: false,
                  //       allowOutsideClick: false
                  // });

                  const formData = new FormData(formElement);

                  const response = await fetch(apiRoute, {
                        method: 'POST',
                        body: formData,
                        headers: {
                              'Accept': 'application/json'
                        }
                  });

                  let data;
                  try {
                        data = await response.json();
                  } catch (jsonError) {
                        throw new Error('Failed to parse JSON response');
                  }

                  if (!response.ok) {
                        throw new Error(data.message || 'Submission failed');
                  }

                  await Swal.fire({
                        text: data.message || 'Operation successful',
                        icon: 'success'
                  });

                  modal.hide();
                  $("select").val(null).trigger('change');
                  $(formElement).find('select').val(null).trigger('change');
                  formElement.reset();

                  if (datatable) datatable.ajax.reload(false, null);

                  onSuccess(data);

            } catch (error) {
                  console.error('Form submission error:', error);
                  await Swal.fire({
                        text: error.message || 'Operation failed',
                        icon: 'error'
                  });
            }
      };

      formElement.removeEventListener('submit', submitHandler); // Remove if already exists
      formElement.addEventListener('submit', submitHandler);     // Add new listener
};
