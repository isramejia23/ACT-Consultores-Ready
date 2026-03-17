<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederCatalogoServicios extends Seeder
{
    public function run(): void
    {
        $servicios = [
            // ARRIENDOS
            ['codigo' => '01', 'nombre' => 'ARRIENDO VIVIENDA JULIO 2025', 'categoria' => 'ARRIENDOS'],

            // HONORARIOS Y ASESORIAS (10xxx)
            ['codigo' => '10000', 'nombre' => 'ASESORIA NEGOCIO POPULAR CON REPORTE', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10001', 'nombre' => 'HONORARIOS ENERO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 1],
            ['codigo' => '10002', 'nombre' => 'HONORARIOS FEBRERO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 2],
            ['codigo' => '10003', 'nombre' => 'HONORARIOS MARZO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 3],
            ['codigo' => '10004', 'nombre' => 'HONORARIOS ABRIL', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 4],
            ['codigo' => '10005', 'nombre' => 'HONORARIOS MAYO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 5],
            ['codigo' => '10006', 'nombre' => 'HONORARIOS JUNIO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 6],
            ['codigo' => '10007', 'nombre' => 'HONORARIOS JULIO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 7],
            ['codigo' => '10008', 'nombre' => 'HONORARIOS AGOSTO', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 8],
            ['codigo' => '10009', 'nombre' => 'HONORARIOS SEPTIEMBRE', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 9],
            ['codigo' => '10010', 'nombre' => 'HONORARIOS OCTUBRE', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 10],
            ['codigo' => '10011', 'nombre' => 'HONORARIOS NOVIEMBRE', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 11],
            ['codigo' => '10012', 'nombre' => 'HONORARIOS DICIEMBRE', 'categoria' => 'HONORARIOS', 'genera_obligacion' => true, 'periodicidad' => 'mensual', 'mes' => 12],
            ['codigo' => '10013', 'nombre' => 'HONORIDADES CONTABLES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10014', 'nombre' => 'ASESORIA ANTICIPO IMPUESTO A LA RENTA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10015', 'nombre' => 'Consultoria NIIFS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10016', 'nombre' => 'ESTADOS FINANCIEROS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10017', 'nombre' => 'ASESORIA IMPUESTO A LA RENTA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10018', 'nombre' => 'ASESORIA SUSTITUTIVA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10019', 'nombre' => 'Flujo de Caja', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10020', 'nombre' => 'HONORARIOS AUDITORIAS', 'categoria' => 'HONORARIOS'],
            ['codigo' => '10021', 'nombre' => 'ASESORIA ENERO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 1],
            ['codigo' => '10022', 'nombre' => 'ASESORIA FEBRERO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 2],
            ['codigo' => '10023', 'nombre' => 'ASESORIA MARZO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 3],
            ['codigo' => '10024', 'nombre' => 'ASESORIA ABRIL N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 4],
            ['codigo' => '10025', 'nombre' => 'ASESORIA MAYO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 5],
            ['codigo' => '10026', 'nombre' => 'ASESORIA JUNIO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 6],
            ['codigo' => '10027', 'nombre' => 'ASESORIA JULIO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 7],
            ['codigo' => '10028', 'nombre' => 'ASESORIA AGOSTO N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 8],
            ['codigo' => '10029', 'nombre' => 'ASESORIA SEPTIEMBRE N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 9],
            ['codigo' => '10030', 'nombre' => 'ASESORIA OCTUBRE N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 10],
            ['codigo' => '10031', 'nombre' => 'ASESORIA NOVIEMBRE N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 11],
            ['codigo' => '10032', 'nombre' => 'ASESORIA DICIEMBRE N/C', 'categoria' => 'ASESORIAS N/C', 'mes' => 12],
            ['codigo' => '10033', 'nombre' => 'SUSTITUTIVA N/C', 'categoria' => 'ASESORIAS N/C'],
            ['codigo' => '10034', 'nombre' => 'ASESORIA D.R. N/C', 'categoria' => 'ASESORIAS N/C'],
            ['codigo' => '10035', 'nombre' => 'Seminario Vehiculo', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10036', 'nombre' => 'ASESORIA DEC. PATRIMONIAL', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10037', 'nombre' => 'ANEXO GASTOS PERSONALES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10038', 'nombre' => 'RUC', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10039', 'nombre' => 'REPORTE', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10040', 'nombre' => 'ANEXO relacion de dependencia', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10041', 'nombre' => 'PROYECCION DE GASTOS PERSONALES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10042', 'nombre' => 'Notificacion', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10043', 'nombre' => 'HERENCIAS FORMULARIO 108', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10044', 'nombre' => 'DEVOLUCION MERCADERIA INCAUTADA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10045', 'nombre' => 'DEVOLUCION IMP RENTA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10046', 'nombre' => 'Devolucion IVA tercera edad', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10047', 'nombre' => 'CIERRE RUC', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10048', 'nombre' => 'SRI Certificado SRI', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10049', 'nombre' => 'SRI Certificado de Habilitacion', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10050', 'nombre' => 'BAJA LIBRETIN', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10051', 'nombre' => 'Anexos Accionistas', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10052', 'nombre' => 'SUSTITUTIVA IMP. A LA RENTA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10053', 'nombre' => 'DECLARACIONES NO CONTABLES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10054', 'nombre' => 'ASESORIA SEMESTRAL N/C', 'categoria' => 'ASESORIAS N/C'],
            ['codigo' => '10055', 'nombre' => 'ATS ANEXO TRANSACCIONAL SIMPLIFICADO', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10056', 'nombre' => 'DECIMO TERCER SUELDO XIII', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10057', 'nombre' => 'DECIMO CUARTO SUELDO XIV', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10058', 'nombre' => 'VENTA DE FORMULARIOS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10059', 'nombre' => 'FORMULARIO 106 MULTAS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10060', 'nombre' => 'ANEXO DE DIVIDENDOS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10061', 'nombre' => 'FACTURAS ELECTRONICAS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10062', 'nombre' => 'CAPACITACION TRIBUTARIA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10063', 'nombre' => 'COMISARIO REVISOR', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10064', 'nombre' => 'FORMULARIO 120 CONT. SOLIDARIA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10065', 'nombre' => 'INDICES SEGURIDAD OCUPACIONAL', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10066', 'nombre' => 'DECLARACION TIERRAS RURALES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10067', 'nombre' => 'ASESORIA D.I.R.CONTABLE', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10068', 'nombre' => 'EXONERACION ARTESANOS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10069', 'nombre' => 'FACTUREROS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10070', 'nombre' => 'I.V.A. PRIMER SEMESTRE MICROEMPRESAS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10071', 'nombre' => 'I.V.A. SEGUNDO SEMESTRE MICROEMPRESAS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10072', 'nombre' => 'FORMULARIO 125 MICROEMPRESAS', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10073', 'nombre' => 'ASESORIA FACT ELECTRONICA', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10074', 'nombre' => 'COMISION POR VENTA DE PAQUETES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10075', 'nombre' => 'ASESORIA NEGOCIO POPULAR SIN REPORTE', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10076', 'nombre' => 'ASESORIA RIMPE - EMPRENDEDOR REPORTE', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10077', 'nombre' => 'ASESORIA D.I.R. NEGOCIOS POPULARES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10078', 'nombre' => 'ASESORIA D.I.R. EMPRENDEDOR', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10079', 'nombre' => 'ASESORIA PUBLICIDAD Y REDES SOCIALES', 'categoria' => 'ASESORIAS'],
            ['codigo' => '10080', 'nombre' => 'SISTEMA CONTABLE', 'categoria' => 'ASESORIAS'],

            // BANCO / REEMBOLSOS (20xxx)
            ['codigo' => '20001', 'nombre' => 'COMISION BANCARIA', 'categoria' => 'BANCO'],
            ['codigo' => '20002', 'nombre' => 'REEMBOLSO IMPUESTOS SRI BANCO', 'categoria' => 'BANCO'],
            ['codigo' => '20003', 'nombre' => 'REEMBOLSO IESS PLANILLAS BANCO', 'categoria' => 'BANCO'],
            ['codigo' => '20004', 'nombre' => 'BANCO Multa Supercias.', 'categoria' => 'BANCO'],
            ['codigo' => '20005', 'nombre' => 'BANCO Multas MRL', 'categoria' => 'BANCO'],
            ['codigo' => '20006', 'nombre' => 'BANCO Multas Municipio', 'categoria' => 'BANCO'],
            ['codigo' => '20007', 'nombre' => 'REEMBOLSO BANCO PATENTE', 'categoria' => 'BANCO'],
            ['codigo' => '20008', 'nombre' => 'BANCO Bomberos', 'categoria' => 'BANCO'],
            ['codigo' => '20009', 'nombre' => 'NO UTILIZAR REEMBOLSO MI NEGOCIO', 'categoria' => 'BANCO'],
            ['codigo' => '20010', 'nombre' => 'ASESORIA REGISTRO DE MARCA SENADI', 'categoria' => 'BANCO'],
            ['codigo' => '20011', 'nombre' => 'REEMBOLSOS FACTURACION ELECTRONICA', 'categoria' => 'BANCO'],
            ['codigo' => '20012', 'nombre' => 'REEMBOLSOS FIRMA ELECTRONICA', 'categoria' => 'BANCO'],

            // MUNICIPIO / PATENTE (30xxx)
            ['codigo' => '30001', 'nombre' => 'PATENTE HONORARIOS', 'categoria' => 'MUNICIPIO'],
            ['codigo' => '30002', 'nombre' => 'Formulario Municipio', 'categoria' => 'MUNICIPIO'],
            ['codigo' => '30003', 'nombre' => 'CIERRE PATENTE', 'categoria' => 'MUNICIPIO'],
            ['codigo' => '30004', 'nombre' => 'Bomberos HONORARIOS', 'categoria' => 'MUNICIPIO'],
            ['codigo' => '30005', 'nombre' => 'MUNICIPIO Permiso Uso de Suelo', 'categoria' => 'MUNICIPIO'],
            ['codigo' => '30006', 'nombre' => 'HONORARIOS PROFESIONALES', 'categoria' => 'MUNICIPIO'],

            // MRL / LABORAL (40xxx)
            ['codigo' => '40001', 'nombre' => 'UTILIDADES MRL', 'categoria' => 'LABORAL'],
            ['codigo' => '40002', 'nombre' => 'CONTRATOS. ACTAS FINIQUITOS ETC', 'categoria' => 'LABORAL'],
            ['codigo' => '40003', 'nombre' => 'XXXX', 'categoria' => 'LABORAL'],
            ['codigo' => '40004', 'nombre' => 'Reglamentos Internos', 'categoria' => 'LABORAL'],
            ['codigo' => '40005', 'nombre' => 'XXXXXXXXXX', 'categoria' => 'LABORAL'],
            ['codigo' => '40006', 'nombre' => 'REGLAMENTO DE SEGURIDAD Y SALUD', 'categoria' => 'LABORAL'],
            ['codigo' => '40007', 'nombre' => 'REGISTRO MINISTERIO DE TRABAJO 2020', 'categoria' => 'LABORAL'],
            ['codigo' => '40008', 'nombre' => 'PLAN MINIMO DE SEGURIDAD', 'categoria' => 'LABORAL'],

            // IESS (50xxx)
            ['codigo' => '50001', 'nombre' => 'Aviso de E y S IESS', 'categoria' => 'IESS'],
            ['codigo' => '50002', 'nombre' => 'PLANILLA IESS HONORARIOS', 'categoria' => 'IESS'],
            ['codigo' => '50003', 'nombre' => 'Roles de Pagos', 'categoria' => 'IESS'],
            ['codigo' => '50004', 'nombre' => 'MECANIZADO', 'categoria' => 'IESS'],

            // SUPERCIAS / GLOSAS (60xxx)
            ['codigo' => '60001', 'nombre' => 'IESS IMPUGNACION GLOSAS', 'categoria' => 'SUPERCIAS'],
            ['codigo' => '60002', 'nombre' => 'BALANCE SUPER. CIAS.', 'categoria' => 'SUPERCIAS'],

            // VARIOS / OFICIOS (70xxx)
            ['codigo' => '70001', 'nombre' => 'OFICIOS', 'categoria' => 'VARIOS'],
            ['codigo' => '70002', 'nombre' => 'SERVICIOS TECNOLOGIA', 'categoria' => 'VARIOS'],
            ['codigo' => '70003', 'nombre' => 'Rup', 'categoria' => 'VARIOS'],
            ['codigo' => '70004', 'nombre' => 'REPORTES', 'categoria' => 'VARIOS'],
            ['codigo' => '70005', 'nombre' => 'Papel Bond', 'categoria' => 'VARIOS'],
            ['codigo' => '70006', 'nombre' => 'IMPRESIONES', 'categoria' => 'VARIOS'],
            ['codigo' => '70007', 'nombre' => 'INCOB', 'categoria' => 'VARIOS'],
            ['codigo' => '70008', 'nombre' => 'Correo Electronico', 'categoria' => 'VARIOS'],
            ['codigo' => '70009', 'nombre' => 'CONTRATO DE ARRIENDO', 'categoria' => 'VARIOS'],
            ['codigo' => '70010', 'nombre' => 'VARIOS', 'categoria' => 'VARIOS'],
            ['codigo' => '70011', 'nombre' => 'PERMISO DE HABILITACION', 'categoria' => 'VARIOS'],
            ['codigo' => '70012', 'nombre' => 'CERTIFICACION ETIQUETAS INEN', 'categoria' => 'VARIOS'],
            ['codigo' => '70013', 'nombre' => 'PERMISOS DE HIGIENE', 'categoria' => 'VARIOS'],
            ['codigo' => '70014', 'nombre' => 'CORREOS Y BASICOS', 'categoria' => 'VARIOS'],
            ['codigo' => '70015', 'nombre' => 'ARCHIVADORES', 'categoria' => 'VARIOS'],
            ['codigo' => '70016', 'nombre' => 'RENOVACION CALIFICACION ARTESANAL', 'categoria' => 'VARIOS'],
            ['codigo' => '70017', 'nombre' => 'PERMISO DE ROTULO', 'categoria' => 'VARIOS'],
            ['codigo' => '70018', 'nombre' => 'PROTOCOLO DE BIOSEGURIDAD DE FICHAS MEDICAS', 'categoria' => 'VARIOS'],
            ['codigo' => '70020', 'nombre' => 'PERMISO RUPTURA DE SUELO', 'categoria' => 'VARIOS'],
            ['codigo' => '70201', 'nombre' => 'CERTIFICADOS', 'categoria' => 'VARIOS'],

            // MATERIAL (80xxx)
            ['codigo' => '800001', 'nombre' => 'MATERIAL PETREO', 'categoria' => 'OTROS'],

            // CODIGOS ESPECIALES (HEAS/OC)
            ['codigo' => 'HEAS:10006343499', 'nombre' => 'HEAS:10006343499', 'categoria' => 'HEAS'],
            ['codigo' => 'HEAS:10006342229', 'nombre' => 'HEAS:10006342229', 'categoria' => 'HEAS'],
            ['codigo' => 'HEAS:10006342230', 'nombre' => 'HEAS:10006342230', 'categoria' => 'HEAS'],
            ['codigo' => 'HEAS:10006343231', 'nombre' => 'HEAS:10006343231', 'categoria' => 'HEAS'],
            ['codigo' => 'OC:4500568511', 'nombre' => 'OC:4500568511', 'categoria' => 'OC'],

            // OTROS FINALES
            ['codigo' => '70021', 'nombre' => 'REEMBOLSO FAUTAJE REDES SOCIALES', 'categoria' => 'VARIOS'],
            ['codigo' => '70022', 'nombre' => 'DOCUMENTOS EN LINEA', 'categoria' => 'VARIOS'],
            ['codigo' => '70023', 'nombre' => 'ARRIENDOS', 'categoria' => 'ARRIENDOS'],
        ];

        foreach ($servicios as $servicio) {
            DB::table('catalogo_servicios')->updateOrInsert(
                ['codigo' => $servicio['codigo']],
                [
                    'nombre' => $servicio['nombre'],
                    'categoria' => $servicio['categoria'] ?? null,
                    'genera_obligacion' => $servicio['genera_obligacion'] ?? false,
                    'periodicidad' => $servicio['periodicidad'] ?? null,
                    'mes' => $servicio['mes'] ?? null,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
