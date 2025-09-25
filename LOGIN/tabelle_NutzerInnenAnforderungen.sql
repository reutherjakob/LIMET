CREATE TABLE room_requirements
(
    id                                INT AUTO_INCREMENT PRIMARY KEY,
    created_at                        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    roomname                          VARCHAR(255) NOT NULL,
    username                          VARCHAR(255) NOT NULL,


    fussboden_onorm_b5220             TINYINT,
    fussboden_onorm_b5220_comment     TEXT,

    verdunkelung                      TINYINT,
    verdunkelung_comment              TEXT,

    schallschutzanforderung           TINYINT,
    schallschutzanforderung_comment   TEXT,

    vc_klasse                         TINYINT,
    vc_klasse_comment                 TEXT,

    chemikalienliste                  TINYINT,
    chemikalienliste_comment          TEXT,

    vexat_zone                        ENUM ('0','1','2'),
    vexat_zone_comment                TEXT,

    bsl_level                         VARCHAR(32),
    bsl_level_comment                 TEXT,

    laser                             TINYINT,
    laser_comment                     TEXT,

    o2                                TINYINT,
    o2_comment                        TEXT,

    va                                TINYINT,
    va_comment                        TEXT,

    dl                                TINYINT,
    dl_comment                        TEXT,

    co2                               TINYINT,
    co2_comment                       TEXT,

    h2                                TINYINT,
    h2_comment                        TEXT,

    he                                TINYINT,
    he_comment                        TEXT,

    he_rf                             TINYINT,
    he_rf_comment                     TEXT,

    ar                                TINYINT,
    ar_comment                        TEXT,

    n2                                TINYINT,
    n2_comment                        TEXT,

    ln                                TINYINT,
    ln_comment                        TEXT,

    spezialgas                        TINYINT,
    spezialgas_comment                TEXT,

    sv_geraete                        TINYINT,
    sv_geraete_comment                TEXT,

    usv_geraete                       TINYINT,
    usv_geraete_comment               TEXT,

    alarm_glt                         TINYINT,
    alarm_glt_comment                 TEXT,

    kuehlwasser                       TINYINT,
    kuehlwasser_comment               TEXT,

    wasserq3                          TINYINT,#def?
    wasserq3_comment                  TEXT,

    wasserq2                          TINYINT,#def?
    wasserq2_comment                  TEXT,

    wasserq1                          TINYINT, #def?
    wasserq1_comment                  TEXT,

    geraete_wasser_abfluss            TINYINT,
    geraete_wasser_abfluss_comment    TEXT,

    punktabsaugung                    TINYINT,
    punktabsaugung_comment            TEXT,

    abluft_sicherheitsschrank         TINYINT,
    abluft_sicherheitsschrank_comment TEXT,

    abluft_vakuumpumpe                TINYINT,
    abluft_vakuumpumpe_comment        TEXT,

    abrauchabzuege                    TINYINT,
    abrauchabzuege_comment            TEXT,

    sonderabluft                      TINYINT,
    sonderabluft_comment              TEXT,

    -- ROOM FIELDS
    roomID                            INT,
    room_comment                      TEXT,
    raumnr                            VARCHAR(255),
    raumbereich_nutzer                VARCHAR(255),
    ebene                             VARCHAR(255),
    nf                                VARCHAR(255)

);
