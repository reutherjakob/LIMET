<?php
// MONEY FORMAT DEFINITIONS:
// USER ENTERS COSTS:       doesn't matter, shall be converted to DB format! Happening in js @interface to sql
// SAVED IN DB:             1234567,89      ("," as decimal separator)
// VISUALIZED IN TABLES:    1.234.567,89    (depends on downloadability. wip. no)
// OUTPUT WITHIN EXCEL:     1234567,89      ("," as decimal separator, focus on workability)
// OUTPUT REPORTS:          1.234.567,89 €

function format_money($number): string // function to format costs for visualization in table
{
    $formattedNumber = sprintf('%s', number_format((float)$number, 2, ',', '.'));
    return $formattedNumber;
}

function format_money_report($number): string // function to format costs for reports
{
    $formattedNumber = sprintf('%s €', number_format((float)$number, 2, ',', '.'));
    return $formattedNumber;
}

