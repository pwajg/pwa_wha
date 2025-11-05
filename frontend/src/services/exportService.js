/**
 * Servicio de exportación de reportes
 * Permite exportar datos en formato PDF o Excel
 */

import * as XLSX from 'xlsx'
import { jsPDF } from 'jspdf'
import html2canvas from 'html2canvas'

/**
 * Exporta datos a Excel
 * @param {Array} datos - Array de objetos con los datos a exportar
 * @param {String} nombreArchivo - Nombre del archivo sin extensión
 * @param {String} titulo - Título del reporte
 */
export const exportarExcel = (datos, nombreArchivo = 'reporte', titulo = 'Reporte') => {
  try {
    // Crear workbook
    const wb = XLSX.utils.book_new()
    
    // Crear worksheet desde datos
    const ws = XLSX.utils.json_to_sheet(datos)
    
    // Agregar título
    XLSX.utils.sheet_add_aoa(ws, [[titulo]], { origin: 'A1' })
    
    // Agregar worksheet al workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Datos')
    
    // Generar archivo
    XLSX.writeFile(wb, `${nombreArchivo}.xlsx`)
    
    return true
  } catch (error) {
    console.error('Error al exportar a Excel:', error)
    return false
  }
}

/**
 * Exporta un elemento HTML a PDF
 * @param {HTMLElement} elemento - Elemento HTML a exportar
 * @param {String} nombreArchivo - Nombre del archivo sin extensión
 * @param {String} titulo - Título del documento
 */
export const exportarPDF = async (elemento, nombreArchivo = 'reporte', titulo = 'Reporte') => {
  try {
    // Crear instancia de jsPDF
    const pdf = new jsPDF('p', 'mm', 'a4')
    
    // Agregar título
    pdf.setFontSize(18)
    pdf.text(titulo, 105, 20, { align: 'center' })
    
    // Convertir HTML a canvas
    const canvas = await html2canvas(elemento, {
      scale: 2,
      useCORS: true,
      logging: false
    })
    
    const imgData = canvas.toDataURL('image/png')
    
    // Calcular dimensiones
    const imgWidth = 210 // A4 width in mm
    const pageHeight = 297 // A4 height in mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width
    let heightLeft = imgHeight
    
    let position = 30 // Start position after title
    
    // Agregar primera página
    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight)
    heightLeft -= pageHeight - position
    
    // Agregar páginas adicionales si es necesario
    while (heightLeft >= 0) {
      position = heightLeft - imgHeight
      pdf.addPage()
      pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight)
      heightLeft -= pageHeight
    }
    
    // Guardar PDF
    pdf.save(`${nombreArchivo}.pdf`)
    
    return true
  } catch (error) {
    console.error('Error al exportar a PDF:', error)
    return false
  }
}

/**
 * Exporta múltiples widgets a un solo PDF
 * @param {Array} elementos - Array de elementos HTML a exportar
 * @param {String} nombreArchivo - Nombre del archivo sin extensión
 * @param {String} titulo - Título del documento
 */
export const exportarMultiplesWidgetsPDF = async (elementos, nombreArchivo = 'reporte-completo', titulo = 'Reporte Completo') => {
  try {
    const pdf = new jsPDF('p', 'mm', 'a4')
    const pageWidth = 210
    const pageHeight = 297
    let yPosition = 20
    
    pdf.setFontSize(18)
    pdf.text(titulo, pageWidth / 2, yPosition, { align: 'center' })
    yPosition += 15
    
    for (let i = 0; i < elementos.length; i++) {
      const elemento = elementos[i]
      
      if (i > 0) {
        pdf.addPage()
        yPosition = 20
      }
      
      const canvas = await html2canvas(elemento, {
        scale: 2,
        useCORS: true,
        logging: false
      })
      
      const imgData = canvas.toDataURL('image/png')
      const imgWidth = pageWidth - 20
      const imgHeight = (canvas.height * imgWidth) / canvas.width
      
      if (yPosition + imgHeight > pageHeight - 20) {
        pdf.addPage()
        yPosition = 20
      }
      
      pdf.addImage(imgData, 'PNG', 10, yPosition, imgWidth, imgHeight)
      yPosition += imgHeight + 10
    }
    
    pdf.save(`${nombreArchivo}.pdf`)
    
    return true
  } catch (error) {
    console.error('Error al exportar múltiples widgets:', error)
    return false
  }
}

/**
 * Exporta datos estructurados a Excel con formato
 * @param {Object} datosEstructurados - Objeto con estructura { titulo: string, datos: Array }
 * @param {String} nombreArchivo - Nombre del archivo sin extensión
 */
export const exportarExcelEstructurado = (datosEstructurados, nombreArchivo = 'reporte') => {
  try {
    const wb = XLSX.utils.book_new()
    
    // Si es un array de objetos con múltiples hojas
    if (Array.isArray(datosEstructurados)) {
      datosEstructurados.forEach((hoja, index) => {
        const ws = XLSX.utils.json_to_sheet(hoja.datos)
        if (hoja.titulo) {
          XLSX.utils.sheet_add_aoa(ws, [[hoja.titulo]], { origin: 'A1' })
        }
        XLSX.utils.book_append_sheet(wb, ws, hoja.nombre || `Hoja${index + 1}`)
      })
    } else {
      // Una sola hoja
      const ws = XLSX.utils.json_to_sheet(datosEstructurados.datos || datosEstructurados)
      if (datosEstructurados.titulo) {
        XLSX.utils.sheet_add_aoa(ws, [[datosEstructurados.titulo]], { origin: 'A1' })
      }
      XLSX.utils.book_append_sheet(wb, ws, 'Datos')
    }
    
    XLSX.writeFile(wb, `${nombreArchivo}.xlsx`)
    
    return true
  } catch (error) {
    console.error('Error al exportar Excel estructurado:', error)
    return false
  }
}

