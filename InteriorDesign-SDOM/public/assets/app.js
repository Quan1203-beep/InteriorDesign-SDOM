const form = document.querySelector('#consult-form');
if (form) {
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const output = document.querySelector('#form-message');
    try {
      const response = await fetch('/consultation', { method: 'POST', body: new FormData(form) });
      const data = await response.json();
      if (!response.ok) throw new Error(Object.values(data.errors || {}).join(' '));
      output.textContent = data.message;
      form.reset();
    } catch (error) {
      output.textContent = error.message || 'Không gửi được yêu cầu. Vui lòng thử lại.';
    }
  });
}
