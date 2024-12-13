<!-- mantenimiento_modales.php -->
<!-- Modal Moneda -->
<div id="modalMoneda" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="tituloModalMoneda">Agregar Moneda</h2>
        
        <form id="formMoneda" method="POST" action="procesar_mantenimiento.php">
            <input type="hidden" name="tipo" value="moneda">
            <input type="hidden" name="id" id="monedaId">
            
            <div class="form-group">
                <label for="codigoMoneda">Código:</label>
                <input type="text" id="codigoMoneda" name="codigo" maxlength="3" required>
            </div>
            
            <div class="form-group">
                <label for="nombreMoneda">Nombre:</label>
                <input type="text" id="nombreMoneda" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="simboloMoneda">Símbolo:</label>
                <input type="text" id="simboloMoneda" name="simbolo" maxlength="5" required>
            </div>
            
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<!-- Modal Ofrenda -->
<div id="modalOfrenda" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="tituloModalOfrenda">Agregar Tipo de Ofrenda</h2>
        
        <form id="formOfrenda" method="POST" action="procesar_mantenimiento.php">
            <input type="hidden" name="tipo" value="ofrenda">
            <input type="hidden" name="id" id="ofrendaId">
            
            <div class="form-group">
                <label for="nombreOfrenda">Nombre:</label>
                <input type="text" id="nombreOfrenda" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="descripcionOfrenda">Descripción:</label>
                <textarea id="descripcionOfrenda" name="descripcion" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<!-- Modal Banco -->
<div id="modalBanco" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="tituloModalBanco">Agregar Banco</h2>
        
        <form id="formBanco" method="POST" action="procesar_mantenimiento.php">
            <input type="hidden" name="tipo" value="banco">
            <input type="hidden" name="id" id="bancoId">
            
            <div class="form-group">
                <label for="nombreBanco">Nombre:</label>
                <input type="text" id="nombreBanco" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="codigoBanco">Código:</label>
                <input type="text" id="codigoBanco" name="codigo" maxlength="20">
            </div>
            
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>