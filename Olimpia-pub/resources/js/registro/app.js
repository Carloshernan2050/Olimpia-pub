import { iniciarAvisos } from '../compartido/avisos';
import { cuandoElDocumentoEsteListo } from '../compartido/cuando-el-documento-este-listo';

cuandoElDocumentoEsteListo(() => {
    iniciarAvisos();
});
