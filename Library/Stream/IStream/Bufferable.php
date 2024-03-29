<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Stream\IStream {

/**
 * Interface \Hoa\Stream\IStream\Bufferable.
 *
 * Interface for bufferable streams. It's complementary to native buffer support
 * of Hoa\Stream (please, see *StreamBuffer*() methods). Classes implementing
 * this interface are able to create nested buffers, flush them etc.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2012 Ivan Enderlin.
 * @license    New BSD License
 */

interface Bufferable {

    /**
     * Start a new buffer.
     * The callable acts like a light filter.
     *
     * @access  public
     * @param   mixed  $call    First callable part.
     * @param   mixed  $able    Second callable part (if needed).
     * @param   int    $size    Size.
     * @return  int
     */
    public function newBuffer ( $call = null, $able = '', $size = null );

    /**
     * Flush the buffer.
     *
     * @access  public
     * @return  void
     */
    public function flush ( );

    /**
     * Delete buffer.
     *
     * @access  public
     * @return  bool
     */
    public function deleteBuffer ( );

    /**
     * Get bufffer level.
     *
     * @access  public
     * @return  int
     */
    public function getBufferLevel ( );

    /**
     * Get buffer size.
     *
     * @access  public
     * @return  int
     */
    public function getBufferSize ( );
}

}
