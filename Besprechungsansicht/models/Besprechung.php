<?php
// models/Besprechung.php

class Besprechung
{
    public int $id;
    public string $name;
    public string $datum;
    public string $startzeit;
    public ?string $endzeit;
    public string $ort;
    public string $verfasser;
    public string $art;
    public int $projektID;

    private $mysqli;

    public function __construct(mysqli $mysqli, array $data)
    {
        $this->mysqli = $mysqli;
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->datum = $data['datum'] ?? '';
        $this->startzeit = $data['startzeit'] ?? '';
        $this->endzeit = $data['endzeit'] ?? null;
        $this->ort = $data['ort'] ?? '';
        $this->verfasser = $data['verfasser'] ?? '';
        $this->art = $data['art'] ?? '';
        $this->projektID = $data['projektID'] ?? 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    public function validate(): array
    {
        $errors = [];
        if (empty($this->name)) $errors[] = "Name darf nicht leer sein.";
        if (empty($this->datum)) $errors[] = "Datum muss angegeben werden.";
        if (empty($this->startzeit)) $errors[] = "Startzeit muss angegeben werden.";
        if (empty($this->verfasser)) $errors[] = "Verfasser muss angegeben werden.";
        // Add more validations as needed
        return $errors;
    }

    public function save(): bool
    {
        if ($this->id > 0) {
            // Update existing if needed in the future
            return false;
        }

        $sql = "INSERT INTO LIMET_RB.tabelle_Vermerkgruppe 
                (Gruppenname, Gruppenart, Ort, Verfasser, Startzeit, Endzeit, Datum, tabelle_projekte_idTABELLE_Projekte) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->mysqli->error);
        }

        $stmt->bind_param(
            "sssssssi",
            $this->name,
            $this->art,
            $this->ort,
            $this->verfasser,
            $this->startzeit,
            $this->endzeit,
            $this->datum,
            $this->projektID
        );

        $res = $stmt->execute();

        if ($res) {
            $this->id = $this->mysqli->insert_id;
        }
        $stmt->close();
        return $res;
    }

    public static function fetchAllByProjekt(mysqli $mysqli, int $projektID): array
    {
        $sql = "SELECT * FROM LIMET_RB.tabelle_Vermerkgruppe WHERE tabelle_projekte_idTABELLE_Projekte = ? ORDER BY Datum DESC, Startzeit DESC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $projektID);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = [];
        while ($row = $result->fetch_assoc()) {
            $list[] = new Besprechung($mysqli, [
                'id' => $row['id'], // Adjust columns names based on your schema
                'name' => $row['Gruppenname'],
                'art' => $row['Gruppenart'],
                'ort' => $row['Ort'],
                'verfasser' => $row['Verfasser'],
                'startzeit' => $row['Startzeit'],
                'endzeit' => $row['Endzeit'],
                'datum' => $row['Datum'],
                'projektID' => $projektID
            ]);
        }
        $stmt->close();
        return $list;
    }


}

