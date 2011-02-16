<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace {

from('Hoa')

/**
 * \Hoa\Console\Core\Exception
 */
-> import('Console.Core.Exception')

/**
 * \Hoa\Console\Environment\Window
 */
-> import('Console.Environment.Window');

/**
 * Whether they are not defined.
 */
_define('STDIN',  fopen('php://stdin' , 'rb'));
_define('STDOUT', fopen('php://stdout', 'wb'));
_define('STDERR', fopen('php://stderr', 'wb'));

}

namespace Hoa\Console\Core {

/**
 * Class \Hoa\Console\Core\Io.
 *
 * This class allows to treat the STDIN, STDOUT, and STDERR stream.
 * Methods have options to make a bit more than just writte in a i/o stream,
 * like wordwrap the text, or prepare a question (y/n) etc.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Io {

    /**
     * Write a \n after the cout message.
     *
     * @cont bool
     */
    const NEW_LINE      = true;

    /**
     * Do not write a \n after the cout message.
     *
     * @const bool
     */
    const NO_NEW_LINE   = false;

    /**
     * Wordwrap text when using cout() method.
     *
     * @const bool
     */
    const WORDWRAP      = true;

    /**
     * Do not wordwrap text when using cout() method.
     *
     * @const bool
     */
    const NO_WORDWRAP   = false;

    /**
     * Whether the cin() should have a normal comportement.
     *
     * @const int
     */
    const TYPE_NORMAL   = 0;

    /**
     * Whether the cin() method should receive an answer to a question. The
     * answer should be : “yes” or “no”.
     *
     * @const int
     */
    const TYPE_YES_NO   = 1;

    /**
     * Whether the cin() method should receive a password. In this case, do not
     * show the password.
     *
     * @const int
     */
    const TYPE_PASSWORD = 2;



    /**
     * Get data from the standard input.
     *
     * @access  public
     * @param   string  $prefix          The prefix text before getting data
     *                                   from STDIN.
     * @param   bool    $comportement    If the cin should receive an answer to
     *                                   a question, a password, or a normal
     *                                   string.
     * @param   bool    $ln              Whether add a \n at the end of data.
     * @param   bool
     * @return  mixed
     * @throw   \Hoa\Console\Core\Exception
     * @todo    Remake the password system (maybe with the fflush() function,
     *          see the unix:///coreutils-6.11/lib/getpass.c)
     */
    public static function cin ( $prefix = null, $comportement = self::TYPE_NORMAL,
                                 $ln     = self::NEW_LINE ) {

        if($comportement === self::TYPE_YES_NO)
            $prefix .= ' (y/n)';

        self::cout($prefix, $ln);

        // Hack for password, bad hack.
        if($comportement === self::TYPE_PASSWORD)
            if(!OS_WIN && function_exists('posix_isatty'))
                self::cout("\033[8m", self::NO_NEW_LINE);

        if(false === $in = fgets(STDIN))
            throw new \Hoa\Console\Core\Exception(
                'Cannot read the standard input.', 0);

        if($comportement === self::TYPE_PASSWORD)
            if(!OS_WIN && function_exists('posix_isatty'))
                self::cout("\033[0m", self::NO_NEW_LINE);

        $in = trim($in);

        if($comportement !== self::TYPE_YES_NO)
            return $in;

        $return = false;

        switch($in) {

            case 'y':
            case 'ye':
            case 'yes':
            case 'yeah': // hihi
                $return = true;
              break;

            default:
                $return = false;
        }

        return $return;
    }

    /**
     * Write data into the standard output.
     *
     * @access  public
     * @param   mixed   $out    Data to write.
     * @param   bool    $ln     Whether add a \n at the end of data.
     * @param   bool    $ww     Wordwrap the text or not.
     * @return  void
     * @throw   \Hoa\Console\Core\Exception
     */
    public static function cout ( $out = null, $ln = self::NEW_LINE,
                                  $ww  = self::WORDWRAP ) {

        if(self::WORDWRAP === $ww)
            $out = wordwrap(
                       $out,
                       \Hoa\Console\Environment\Window::getColumns(),
                       "\n",
                       true
                   );

        if(self::NEW_LINE === $ln)
            $out .= "\n";

        if(false === @fwrite(STDOUT, $out))
            throw new Exception(
                'Cannot write in the standard output. Data was %s.', 1, $out);

        return;
    }
}

}

namespace {

/**
 * Alias of \Hoa\Console\Core\Io::cin.
 *
 * @access  public
 * @param   string  $prefix          The prefix text before getting data
 *                                   from STDIN.
 * @param   bool    $comportement    If the cin should receive an answer to
 *                                   a question, a password, or a normal
 *                                   string.
 * @param   bool    $ln              Whether add a \n at the end of data.
 * @return  string
 * @throw   \Hoa\Console\Core\Exception
 */
if(!ƒ('cin')) {
function cin ( $prefix       = null,
               $comportement = \Hoa\Console\Core\Io::TYPE_NORMAL,
               $ln           = \Hoa\Console\Core\Io::NEW_LINE ) {

    return \Hoa\Console\Core\Io::cin($prefix, $comportement, $ln);
}}

/**
 * Alias of \Hoa\Console\Core\Io::cout.
 *
 * @access  public
 * @param   mixed   $out    Data to write.
 * @param   bool    $ln     Whether add a \n at the end of data.
 * @param   bool    $ww     Wordwrap the text or not.
 * @return  void
 * @throw   \Hoa\Console\Core\Exception
 */
if(!ƒ('cout')) {
function cout ( $out = null, $ln = \Hoa\Console\Core\Io::NEW_LINE,
                $ww  = \Hoa\Console\Core\Io::WORDWRAP ) {

    return \Hoa\Console\Core\Io::cout($out, $ln, $ww);
}}

}
