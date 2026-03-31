<?php
require_once __DIR__ . '/db.php';

class Concepten {

    public static function opslaan($klantId, $naam, $config) {
        // Check of er al een concept bestaat met dezelfde naam
        $bestaand = DB::fetch(
            'SELECT id FROM concepten WHERE klant_id = ? AND naam = ?',
            [$klantId, $naam]
        );
        if ($bestaand) {
            DB::run(
                'UPDATE concepten SET configuratie = ?, bijgewerkt = NOW() WHERE id = ?',
                [json_encode($config), $bestaand['id']]
            );
            return $bestaand['id'];
        }
        return DB::insert(
            'INSERT INTO concepten (klant_id, naam, configuratie) VALUES (?,?,?)',
            [$klantId, $naam, json_encode($config)]
        );
    }

    public static function vanKlant($klantId) {
        $concepten = DB::fetchAll(
            'SELECT id, naam, aangemaakt, bijgewerkt FROM concepten WHERE klant_id = ? ORDER BY bijgewerkt DESC',
            [$klantId]
        );
        return $concepten;
    }

    public static function ophalen($id, $klantId) {
        $concept = DB::fetch(
            'SELECT * FROM concepten WHERE id = ? AND klant_id = ?',
            [$id, $klantId]
        );
        if (!$concept) return null;
        $concept['configuratie'] = json_decode($concept['configuratie'], true);
        return $concept;
    }

    public static function verwijder($id, $klantId) {
        $stmt = DB::run(
            'DELETE FROM concepten WHERE id = ? AND klant_id = ?',
            [$id, $klantId]
        );
        return $stmt->rowCount() > 0;
    }
}
