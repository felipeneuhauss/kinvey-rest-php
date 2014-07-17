<?php
/**
 * Exibe informacoes relacionadas a expressao. Se o segundo parametro for
 * TRUE a execucao e interrompida
 *
 * @param MIX $mixExpression
 * @param BOOLEAN $boolExit
 * @return VOID
 */
function debug( $mixExpression , $boolExit = FALSE )
{
    $arrBacktrace = debug_backtrace();
    $strMessage = "<fieldset><legend><font color=\"#007000\">debug</font></legend><pre>" ;
    foreach ( $arrBacktrace[ 0 ] as $strAttribute => $mixValue )
    {
        if ( ( $strAttribute != "class" ) && ( $strAttribute != "object" ) && ( $strAttribute != "args" ) )
        {
            if ( $strAttribute == "type" )
            {
                $strMessage .= "<b>" . $strAttribute . "</b> ". gettype( $mixExpression ) ."\n";
            }
            else
            {
                $strMessage .= "<b>" . $strAttribute . "</b> ". $mixValue ."\n";
            }
        }
    }
    $strMessage .= "<hr />";
    ob_start();
    var_dump( $mixExpression );
    $strMessage .= ob_get_clean();
    $strMessage .= "</pre></fieldset>";
    print $strMessage;
    if ( $boolExit )
    {
        print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";
        die();
    }
}
