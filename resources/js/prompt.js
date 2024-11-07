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
      formId,          // Form ID (e.g., 'add-customer-form')
      modalId,         // Modal ID (e.g., 'add-customer-modal')
      apiRoute,        // API endpoint (e.g., `${APP_URL}/api/customers`)
      promptMessage,   // Confirmation message (e.g., 'Are you sure you want to add this customer')
      onSuccess = () => { },  // Callback for successful submission
      table = null     // Optional DataTable reference
) => {
      const formElement = document.getElementById(formId);
      const modalElement = document.getElementById(modalId);

      if (!formElement || !modalElement) {
            console.error('Form or modal element not found');
            return;
      }

      // Initialize the modal using Bootstrap
      const modal = new bootstrap.Modal(modalElement);
      const datatable = table ? $(table).DataTable() : null;

      // Listen for form submission
      formElement.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                  // Show confirmation dialog with SweetAlert
                  const confirmResult = await Swal.fire({
                        title: promptMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Submit'
                  });

                  if (!confirmResult.isConfirmed) return;

                  // Show loading dialog
                  await Swal.fire({
                        text: "Processing...",
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false
                  });

                  const formData = new FormData(formElement);

                  // Make API request
                  const response = await fetch(apiRoute, {
                        method: 'POST',
                        body: formData,
                        headers: {
                              'Accept': 'application/json'
                        }
                  });

                  const data = await response.json();

                  if (!response.ok) {
                        throw new Error(data.message || 'Submission failed');
                  }

                  // Show success dialog
                  await Swal.fire({
                        text: data.message || 'Operation successful',
                        icon: 'success'
                  });

                  // Hide modal and reset form
                  modal.hide();
                  $(formElement).find('select').val(null).trigger('change');
                  formElement.reset();

                  // Reload DataTable if provided
                  if (datatable) datatable.ajax.reload(false, null);

                  // Call the onSuccess callback
                  onSuccess(data);

            } catch (error) {
                  console.error('Form submission error:', error);
                  await Swal.fire({
                        text: error.message || 'Operation failed',
                        icon: 'error'
                  });
            }
      });
};