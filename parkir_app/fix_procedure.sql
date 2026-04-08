DELIMITER $$

CREATE PROCEDURE `sp_hitung_biaya` (
    IN `p_id_transaksi` INT, 
    OUT `p_biaya_total` DECIMAL(10,2)
)
BEGIN
    DECLARE v_tarif_per_jam DECIMAL(10,2);
    DECLARE v_durasi_jam DECIMAL(5,2);
    DECLARE v_id_tarif INT;
    
    SELECT id_tarif, durasi_jam INTO v_id_tarif, v_durasi_jam
    FROM tb_transaksi WHERE id_parkir = p_id_transaksi;
    
    SELECT tarif_per_jam INTO v_tarif_per_jam
    FROM tb_tarif WHERE id_tarif = v_id_tarif;
    
    SET p_biaya_total = v_durasi_jam * v_tarif_per_jam;
    
    UPDATE tb_transaksi 
    SET biaya_total = p_biaya_total 
    WHERE id_parkir = p_id_transaksi;
END$$

DELIMITER ;
