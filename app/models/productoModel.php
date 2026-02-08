<?php

declare(strict_types=1);

require_once __DIR__ . "/../../config/conexion.php";

class Producto
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::conectar();
    }

    /* ============================================================
       1. PRODUCTO PADRE
    ============================================================ */

    public function obtenerProductoPorId(int $id): ?array
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.estado, c.nombre AS categoria, c.id AS id_categoria 
                FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id WHERE p.id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function getProductosFull(): array
    {
        $sql = "SELECT p.id, p.nombre, p.estado, p.descripcion, c.nombre as categoria 
                FROM producto p INNER JOIN categoria c ON p.id_categoria = c.id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarProducto(string $nombre, string $descripcion, int $categoria, int $estado): ?int
    {
        $sql = "INSERT INTO producto (id_categoria, nombre, descripcion, estado) VALUES (:categoria, :nombre, :descripcion, :estado)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':categoria' => $categoria, ':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado])
            ? (int)$this->conexion->lastInsertId() : null;
    }

    public function actualizarProducto(int $id, string $nombre, string $descripcion, int $categoria, int $estado): bool
    {
        $sql = "UPDATE producto SET id_categoria = :categoria, nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id = :id";
        return $this->conexion->prepare($sql)->execute([':categoria' => $categoria, ':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado, ':id' => $id]);
    }

    public function isExisteProducto(string $nombre, int $idExcluir = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM producto WHERE LOWER(nombre) = :nombre AND id != :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':nombre' => strtolower($nombre), ':id' => $idExcluir]);
        return $stmt->fetchColumn() > 0;
    }

    /* ============================================================
       2. VARIANTES Y EAV
    ============================================================ */

    public function getVariantesPorProducto(int $id): array
    {
        $sql = "SELECT 
                v.id, 
                v.nombre_variante AS nombre, 
                v.precio_venta, 
                v.stock, 
                v.sku, 
                v.comision,
                v.reserva,
                p.nombre AS nombre_producto_padre,
                c.nombre AS nombre_categoria,
                -- Traemos la imagen principal o la primera que encuentre
                IFNULL((SELECT ruta_imagen FROM variante_imagen WHERE id_variante = v.id ORDER BY es_principal DESC, id ASC LIMIT 1), 'default.webp') as imagen
            FROM variante v
            INNER JOIN producto p ON v.id_producto = p.id
            INNER JOIN categoria c ON p.id_categoria = c.id
            WHERE v.id_producto = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVariantePorId(int $id): ?array
    {
        // Usamos LEFT JOIN para no perder los datos de la variante si no hay fotos
        // Usamos COALESCE para poner una imagen por defecto si la ruta viene NULL
        $sql = "SELECT 
                v.*, 
                COALESCE(m.ruta_imagen, 'default.png') as imagen 
            FROM variante v 
            LEFT JOIN variante_imagen m ON m.id_variante = v.id AND m.es_principal = 1 
            WHERE v.id = :id";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Retornamos el array o null si no se encuentra el ID
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            // Log de error si algo sale mal con la base de datos
            error_log("Error en getVariantePorId: " . $e->getMessage());
            return null;
        }
    }

    public function registrarVarianteValor(int $idV, int $idA, string $valor): bool
    {
        $sql = "INSERT INTO variantevalor (id_variante, id_atributo, valor) VALUES (?, ?, ?)";
        return $this->conexion->prepare($sql)->execute([$idV, $idA, trim($valor)]);
    }

    public function actualizarValorAtributo(int $idV, int $idA, string $valor): bool
    {
        $sql = "UPDATE variantevalor SET valor = :val WHERE id_variante = :idV AND id_atributo = :idA";
        return $this->conexion->prepare($sql)->execute([':val' => trim($valor), ':idV' => $idV, ':idA' => $idA]);
    }

    public function obtenerValorAtributosVariante(int $id): array
    {
        $sql = "SELECT va.id AS id_atributo, va.nombre AS nombre_atributo, vv.valor 
                FROM variantevalor vv INNER JOIN atributo va ON vv.id_atributo = va.id WHERE vv.id_variante = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarVariante(int $idP, string $sku, string $hash, string $nom, float $pC, float $pV, int $sA, int $sM, float $com): ?int
    {
        $sql = "INSERT INTO variante (id_producto, sku, hash_combinacion, nombre_variante, precio_compra, precio_venta, stock, reserva, comision) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$idP, $sku, $hash, $nom, $pC, $pV, $sA, $sM, $com]) ? (int)$this->conexion->lastInsertId() : null;
    }

    // 2. En el método actualizarDatosVariante
    public function actualizarDatosVariante(int $id, string $sku, string $hash, int $stock, float $pV, int $sM, float $com): bool
    {
        // Cambiamos hash_combination -> hash_combinacion
        $sql = "UPDATE variante SET sku = :sku, hash_combinacion = :hash,stock=:stock, precio_venta = :pv, reserva = :sm, comision = :com WHERE id = :id";
        return $this->conexion->prepare($sql)->execute([':sku' => $sku, ':hash' => $hash, ':stock' => $stock, ':pv' => $pV, ':sm' => $sM, ':com' => $com, ':id' => $id]);
    }

    /* ============================================================
       3. IDENTIDAD (HASH Y SKU)
    ============================================================ */

    public function generarHashVariante(array $attr): string
    {
        $p = [];
        foreach ($attr as $id => $v) {
            $p[] = $id . ":" . mb_strtolower(trim((string)$v), 'UTF-8');
        }
        sort($p);
        return md5(implode("|", $p));
    }

    public function generarSkuAleatorio(int $len = 8): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $sku = substr(str_shuffle($chars), 0, $len);
        } while ($this->isExisteSku($sku));
        return $sku;
    }

    public function isExisteSku(string $sku): bool
    {
        $sql = "SELECT COUNT(*) FROM variante WHERE sku = :sku";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':sku' => $sku]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeHashEnProducto(int $idP, string $h): bool
    {
        $sql = "SELECT COUNT(*) FROM variante WHERE id_producto = :idP AND hash_combinacion = :h";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':idP' => $idP, ':h' => $h]);
        return $stmt->fetchColumn() > 0;
    }

    public function existeHashEnOtro(int $idP, string $h, int $idV): bool
    {
        $sql = "SELECT COUNT(*) FROM variante WHERE id_producto = :idP AND hash_combinacion = :h AND id != :idV";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':idP' => $idP, ':h' => $h, ':idV' => $idV]);
        return $stmt->fetchColumn() > 0;
    }

    /* ============================================================
       4. EXPANSIÓN Y CONTRATOS (Aquí estaba el que faltaba)
    ============================================================ */

    public function obtenerAtributosProducto(int $id): array
    {
        $sql = "SELECT id_atributo FROM productoatributo WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIdsAtributosDeProducto(int $id): array
    {
        $sql = "SELECT id_atributo FROM productoatributo WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function verificarVentasProducto(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM detalleventa dv INNER JOIN variante v ON dv.id_variante = v.id WHERE v.id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function inyectarValorNA(int $idP, int $idA): bool
    {
        $sqlAttr = "SELECT nombre FROM atributo WHERE id = :id";
        $stmtA = $this->conexion->prepare($sqlAttr);
        $stmtA->execute([':id' => $idA]);
        $nomA = $stmtA->fetchColumn();

        $sqlEAV = "INSERT INTO variantevalor (id_variante, id_atributo, valor)
                   SELECT v.id, :idA, 'N/A' FROM variante v 
                   WHERE v.id_producto = :idP AND v.id NOT IN (SELECT vv.id_variante FROM variantevalor vv WHERE vv.id_atributo = :idA2)";
        $this->conexion->prepare($sqlEAV)->execute([':idA' => $idA, ':idP' => $idP, ':idA2' => $idA]);

        $sqlNom = "UPDATE variante SET nombre_variante = CONCAT(nombre_variante, ' / ', :nom, ': N/A') WHERE id_producto = :idP";
        return $this->conexion->prepare($sqlNom)->execute([':nom' => $nomA, ':idP' => $idP]);
    }

    public function eliminarAtributoDelContrato(int $idP, int $idA): void
    {
        $this->conexion->prepare("DELETE FROM productoatributo WHERE id_producto = ? AND id_atributo = ?")->execute([$idP, $idA]);
        $this->conexion->prepare("DELETE FROM variantevalor WHERE id_atributo = ? AND id_variante IN (SELECT id FROM variante WHERE id_producto = ?)")->execute([$idA, $idP]);
    }

    public function registrarProductoAtributo(int $idP, int $idA): bool
    {
        return $this->conexion->prepare("INSERT INTO productoatributo (id_producto, id_atributo) VALUES (?, ?)")->execute([$idP, $idA]);
    }

    /* ============================================================
       5. UTILIDADES
    ============================================================ */

    public function generarNombreVarianteDesdeAtributos(int $idV): string
    {
        $stmtP = $this->conexion->prepare("SELECT p.nombre FROM producto p INNER JOIN variante v ON p.id = v.id_producto WHERE v.id = ?");
        $stmtP->execute([$idV]);
        $base = $stmtP->fetchColumn();

        $stmtA = $this->conexion->prepare("SELECT valor FROM variantevalor WHERE id_variante = ? ORDER BY id_atributo ASC");
        $stmtA->execute([$idV]);
        return $base . " / " . implode(" / ", $stmtA->fetchAll(PDO::FETCH_COLUMN));
    }

    public function actualizarNombreVariante(int $id, string $nombre): bool
    {
        return $this->conexion->prepare("UPDATE variante SET nombre_variante = ? WHERE id = ?")->execute([$nombre, $id]);
    }

    public function actualizarImagenVariante(int $id, string $img): bool
    {
        return $this->conexion->prepare("UPDATE variante SET imagen = ? WHERE id = ?")->execute([$img, $id]);
    }

    public function getAtributos(): array
    {
        return $this->conexion->query("SELECT * FROM atributo")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isExisteAtributo(string $nom): bool
    {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM atributo WHERE LOWER(nombre) = ?");
        $stmt->execute([strtolower($nom)]);
        return $stmt->fetchColumn() > 0;
    }

    public function registrarAtributo(string $nom): ?int
    {
        $stmt = $this->conexion->prepare("INSERT INTO atributo (nombre) VALUES (?)");
        return $stmt->execute([$nom]) ? (int)$this->conexion->lastInsertId() : null;
    }

    public function obtenerTodosLosProductosConVariantes(): array
{
    $sql = "SELECT 
            v.id, 
            v.nombre_variante AS nombre, 
            v.precio_venta, 
            v.stock, 
            v.imagen, 
            v.sku, 
            v.comision,
            v.reserva,
            p.nombre AS nombre_producto_padre,
            p.id AS id_producto,
            c.nombre AS nombre_categoria,
            c.id AS id_categoria
        FROM variante v
        INNER JOIN producto p ON v.id_producto = p.id
        INNER JOIN categoria c ON p.id_categoria = c.id
        WHERE p.estado = 1
        ORDER BY c.nombre, p.nombre, v.nombre_variante";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /* ============================================================
        6. GESTIÓN DE GALERÍA DE VARIANTES
    ============================================================ */

    public function registrarImagenVariante(int $idV, string $ruta, int $principal = 0): ?int
    {
        $sql = "INSERT INTO variante_imagen (id_variante, ruta_imagen, es_principal) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        // Si se ejecuta con éxito, devolvemos el ID, si no, null
        return $stmt->execute([$idV, $ruta, $principal]) ? (int)$this->conexion->lastInsertId() : null;
    }

    public function getGaleriaVariante(int $idV): array
    {
        $sql = "SELECT id, ruta_imagen, es_principal, id_variante FROM variante_imagen WHERE id_variante = ? ORDER BY es_principal DESC, id ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idV]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarImagenBD(int $idImg): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT id_variante, ruta_imagen, es_principal FROM variante_imagen WHERE id = ?");
            $stmt->execute([$idImg]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$img) return ["status" => "error", "message" => "No existe"];

            $this->conexion->beginTransaction();

            $this->conexion->prepare("DELETE FROM variante_imagen WHERE id = ?")->execute([$idImg]);

            // Lógica de ingeniería: si borramos la portada, el grid principal TIENE que refrescarse
            // independientemente de si queda otra imagen o no.
            $debeRefrescarGrid = ($img['es_principal'] == 1);

            if ($debeRefrescarGrid) {
                $sqlSucesor = "UPDATE variante_imagen SET es_principal = 1 WHERE id_variante = ? ORDER BY id ASC LIMIT 1";
                $this->conexion->prepare($sqlSucesor)->execute([$img['id_variante']]);
            }

            $this->conexion->commit();

            return [
                "status" => "success",
                "ruta" => $img['ruta_imagen'],
                "id_variante" => $img['id_variante'],
                "refreshGrid" => $debeRefrescarGrid // <--- Esto asegura que el JS actúe
            ];
        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) $this->conexion->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function setearPrincipal(int $idV, int $idImg): bool
    {
        // Solo abre la transacción si no hay una activa ya
        $transaccionPropia = !$this->conexion->inTransaction();

        try {
            if ($transaccionPropia) $this->conexion->beginTransaction();

            // 1. Quitamos principal a todas
            $this->conexion->prepare("UPDATE variante_imagen SET es_principal = 0 WHERE id_variante = ?")->execute([$idV]);
            // 2. Seteamos la nueva
            $this->conexion->prepare("UPDATE variante_imagen SET es_principal = 1 WHERE id = ?")->execute([$idImg]);

            if ($transaccionPropia) $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            if ($transaccionPropia) $this->conexion->rollBack();
            return false;
        }
    }
}
