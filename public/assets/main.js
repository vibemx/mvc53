let catalogos = {}; // Variable global para almacenar el catálogo de mesas
document.addEventListener("DOMContentLoaded", async function () {
  BlockUI();
  await inicializarAplicacion();
  const initEvent = new Event('InitSystem');
  document.dispatchEvent(initEvent);
});
// Función para inicializar la aplicación
async function inicializarAplicacion() {

  try {
    console.log('Iniciando aplicación')
    unblockUI();
  } catch (error) {
    unblockUI();
    Swal.fire({
      title: 'Error al cargar la aplicación',
      text: error.message || "Error",
      icon: "error",
      draggable: true,
    });
  }


}

